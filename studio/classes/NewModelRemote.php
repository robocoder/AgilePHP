<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.studio.classes
 */

/**
 * Remoting class responsible for server side processing on behalf of New Model wizard.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.classes
 */
class NewModelRemote {

    public function __construct() {}

    /**
     * Returns a list of database table names for the current database connection.
     *
     * @param stdClass $database A stdClass instance containing client side parameters
     */
    #@RemoteMethod
    public function getDatabaseTables($workspace, $projectName) {

        $workspace = preg_replace('/\|/', DIRECTORY_SEPARATOR, $workspace);

        $ormXml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml';

        $orm = ORMFactory::load($ormXml);

        $tables = array();

        $Database = $orm->reverseEngineer();

        foreach($Database->getTables() as $table) {

            $t = array();
            $t[0] = $table->getName();

            array_push($tables, $t);
        }

        return $tables;
    }

    #@RemoteMethod
    public function getTableColumns($workspace, $projectName, $tableName) {

        $columns = array();

        $workspace = preg_replace('/\|/', DIRECTORY_SEPARATOR, $workspace);

        $ormXml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml';

        $orm = ORMFactory::load($ormXml);

        $Database = $orm->reverseEngineer();

        foreach($Database->getTables() as $table) {

            if($table->getName() == $tableName) {

                foreach($table->getColumns() as $column) {

                    $c = array();
                    $c[0] = $column->getName();

                    array_push($columns, $c);
                }
            }
        }

        return $columns;
    }

    #@RemoteMethod
    public function getTableColumnsMeta($workspace, $projectName, $tableName) {

        $data = array();

        $workspace = preg_replace('/\|/', DIRECTORY_SEPARATOR, $workspace);

        $ormXml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml';

        $orm = ORMFactory::load($ormXml);

        $Database = $orm->reverseEngineer();

        foreach($Database->getTables() as $table) {

            if($table->getName() == $tableName) {

                foreach($table->getColumns() as $column) {

                    $display = ucfirst(preg_replace('/[_\-\+\!@#\$%\^&\*\(\)]/', '', $column->getName())); // create default display name

                    $d = array();
                    $d[0] = strtolower($column->getName());
                    $d[1] = $column->getName();
                    $d[2] = $column->getDisplay() ? $column->getDisplay() : $display;
                    $d[3] = $column->getType() ? $column->getType() : "";
                    $d[4] = $column->getLength() ? $column->getLength() : 0;
                    $d[5] = $column->getDefault() ? $column->getDefault() : "(null)";
                    $d[6] = $column->isVisible() ? 1 : 0;
                    $d[7] = $column->isRequired() ? 1 : 0;
                    $d[8] = $column->isIndex() ? 1 : 0;
                    $d[9] = $column->isPrimaryKey() ? 1 : 0;
                    $d[10] = $column->isAutoIncrement() ? 1 : 0;
                    $d[11] = 1; // sortable
                    $d[12] = 1; // sanitize

                    array_push($data, $d);
                }
            }
        }

        return $data;
    }

    #@RemoteMethod
    // @todo Pass in workspace and project to look at orm.xml database type and only display the data types for the database being manipulated
    public function getSQLDataTypes() {

        $types = array();
        $values = array('boolean', 'integer', 'int', 'bigint', 'double', 'decimal', 'varchar', 'float', 'bit', 'date', 'datetime', 'timestamp',  // mysql, sqlite
	  		 				  'serial', 'bigserial', 'real', 'numeric', 'box', 'bytea', 'cidr', 'circle', 'inet', 'interval', 'line', 'lseg', 'macaddr', 'path', 'point', 'polygon', 'uuid', 'xml', // pgsql
	                          'blob', 'text', 'password', 'smallint', 'tinyint', 'money', 'char', 'varbinary', 'nvarchar', 'image', 'uniqueidentifier', 'smalldatetime', 'ntext', 'nchar'); // mssql

        foreach($values as $type) {

            $t = array();
            $t[0] = $type;

            array_push($types, $t);
        }

        sort($types);
        return $types;
    }

    #@RemoteMethod
    public function create($tableName, $workspace, $projectName, $properties, $updateOrmDotXml, $createTable) {

        $generator = new ModelGenerator();
        $lineBreak = PHP_EOL;

        $workspace = preg_replace('/\|/', DIRECTORY_SEPARATOR, $workspace);
        $modelName = ucfirst(preg_replace('/[\-\+\!@#\$%\^&\*\(\)]/', '', $tableName));
        $path = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'model';

        $ormXml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml';
        $orm = ORMFactory::load($ormXml);

        $Table = new Table();
        $Table->setName($tableName);
        $Table->setModel($modelName);

        $className = ucfirst($generator->toCamelCase($modelName));

        // Create constructor arguments and getters/setters
        $constructorArgs = '';
        $constructorBody = '';
        $setters = array();
        $getters = array();
         
        for($i=0; $i<count($properties); $i++) {

            // Convert field to camel case
            $field = $generator->toCamelCase(preg_replace('/[\-\+\!@#\$%\^&\*\(\)]/', '', $properties[$i][0]));

            // Define interceptors for built-in AgilePHP components
            $interceptor = null;
            if($field == 'id') $interceptor = '#@Id';
            if($field == 'password') $interceptor = '#@Password';

            $default = $properties[$i][5];

            $constructorArgs .= '$' . $field . ' = ' . ($default ? '\'' . $default . '\'' : 'null');
            $constructorBody .= "        \$this->{$field} = \${$field};{$lineBreak}";

            $setterName = 'set' . ucfirst($field);
            $getterName = 'get' . ucfirst($field);

            $setter = '';
            if(isset($interceptor)) $setter .= '    ' . $interceptor . PHP_EOL;

            $setter .= "    public function {$setterName}(\$$field) {{$lineBreak}        \$this->{$field} = \$$field;{$lineBreak}    }";

            array_push($setters, $setter);
            array_push($getters, "    public function {$getterName}() {{$lineBreak}        return \$this->{$field};{$lineBreak}    }");

            if(($i+1) < count($properties))
            $constructorArgs .= ', ';
        }

        // Begin class
        $code = '<?php' . PHP_EOL . PHP_EOL . '/** AgilePHP generated domain model */' . PHP_EOL . PHP_EOL .
		         "class {$className} extends DomainModel {{$lineBreak}";

        // Fields / properties
        foreach($properties as $field) {

            $default = $field[5];
            $field = $generator->toCamelCase($field[0]);
            $code .= "{$lineBreak}    private \${$field}" . ($default ? ' = \'' . $default . '\'' : '') . ";";
        }

        // Constructor
        $code .= "{$lineBreak}{$lineBreak}    public function __construct({$constructorArgs}) {{$lineBreak}{$constructorBody}    }{$lineBreak}{$lineBreak}";

        // Getters and setters
        for($i=0; $i<count($setters); $i++) {

            $code .= $setters[$i] . $lineBreak . $lineBreak;
            $code .= $getters[$i] . $lineBreak;

            if(($i+1) <count($setters))
            $code .= $lineBreak;
        }

        // End class
        $code .= '}';


        $file = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'model' .
        DIRECTORY_SEPARATOR . $className . '.php';
        $h = fopen($file, 'w');
        fwrite($h, $code);
        fclose($h);

        // Update orm.xml
        if($updateOrmDotXml) {

            $orm_xml = $workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml';
            if(!file_exists($orm_xml))
            throw new FrameworkException('Could not update orm.xml. File does not exist at \'' . $orm_xml . '\'');

            $xml = simplexml_load_file($orm_xml);

            foreach($xml->database->table as $tableXml) {

                if((string)$tableXml->attributes()->model == $modelName)
                throw new FrameworkException('Failed to update orm.xml. Table element already exists for model \'' . $modelName . '\'.');
            }

            $xml = "\t<table name=\"" . $tableName . "\" model=\"" . $modelName . "\">" . PHP_EOL;

            foreach($properties as $value) {

                $Column = new Column(null, $tableName);
                $Column->setProperty($value[0]);
                $Column->setName($value[1]);
                $Column->setDisplay($value[2]);
                $Column->setType($value[3]);
                $Column->setLength($value[4]);
                $Column->setDefault($value[5]);
                $Column->setVisible($value[6]);
                $Column->setRequired($value[7]);
                $Column->setIndex($value[8]);
                $Column->setPrimaryKey($value[9]);
                $Column->setAutoIncrement($value[10]);
                $Column->setSortable($value[11]);
                $Column->setSanitize($value[12]);

                $Table->addColumn($Column);

                $xml .= "\t\t\t<column name=\"" . $Column->getName() . "\" type=\"" . $Column->getType() . "\" length=\"" . $Column->getLength() . "\"";

                $xml .= ($Column->getDefault() && $Column->getDefault() != '(null)') ? " default=\"" . $Column->getDefault() . "\"" : '';
                $xml .= $Column->isRequired() ? " required=\"true\"" : '';
                $xml .= (!$Column->isVisible()) ? " visible=\"false\"" : '';
                $xml .= $Column->isIndex() ? " index=\"true\"" : '';
                $xml .= $Column->isPrimaryKey() ? " primaryKey=\"true\"" : '';
                $xml .= $Column->isAutoIncrement() ? " autoIncrement=\"true\"" : '';
                $xml .= (!$Column->isSortable()) ? " sortable=\"false\"" : '';
                $xml .= (!$Column->getSanitize()) ? " sanitize=\"false\"" : '';

                $xml .= '/>' . PHP_EOL;
            }

            $xml .= "\t\t</table>" . PHP_EOL . "\t</database>" . PHP_EOL;

            $h = fopen($workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR . 'orm.xml', 'r');
            $data = '';
            while(!feof($h))
            $data .= fgets($h, 4096);
            fclose($h);

            $h = fopen($workspace . DIRECTORY_SEPARATOR . $projectName . DIRECTORY_SEPARATOR  . 'orm.xml', 'w');
            fwrite($h, str_replace('</database>' . PHP_EOL, $xml, $data));
            fclose($h);
        }

        if($createTable) $orm->createTable($Table);

        return true;
    }
}
?>
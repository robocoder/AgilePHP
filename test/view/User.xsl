<?xml version="1.0" encoding="ISO-8859-1"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/Results">
  <html>
  <body>
  <h2>AgilePHP Framework Test Package Users</h2>
  <table border="1">
    <tr bgcolor="#c9c9c9">
      <th>Username</th>
      <th>Password</th>
      <th>Email</th>
      <th>Created</th>
      <th>Last Login</th>
      <th>Role</th>
    </tr>
    <xsl:for-each select="User">
	    <tr>
	      <td><xsl:value-of select="username"/></td>
	      <td><xsl:value-of select="password"/></td>
	      <td><xsl:value-of select="email"/></td>
	      <td><xsl:value-of select="created"/></td>
	      <td><xsl:value-of select="lastLogin"/></td>
	      <td><xsl:value-of select="Role/name"/></td>
	    </tr>
    </xsl:for-each>
  </table>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>
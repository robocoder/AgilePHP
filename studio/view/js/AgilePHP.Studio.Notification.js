AgilePHP.Studio.Notification = function(title, message) {

    var msgCt;

    this.createBox = function(t, s) {

    	var left;
    	var top;

    	if(AgilePHP.Studio.Notification.notes.length) {

    		for(var i=0; i<AgilePHP.Studio.Notification.notes.length; i++) {

    			 var variant = (i+1) * 80;

	    		 left = document.body.clientWidth - 160;
	    		 top = document.body.clientHeight - (100 + variant);
    		}
    	}
    	else {
		    	left = document.body.clientWidth - 160;
		    	top = document.body.clientHeight - 100;
    	}

    	AgilePHP.Studio.Notification.notes.push(title);

        return ['<div class="msg" style="float: left; z-index: 100000; position: absolute; left: ' + left + 'px; top: ' + top + 'px">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
    }

	if(!msgCt) msgCt = Ext.DomHelper.insertFirst(document.body, {id: 'msg-div'}, true);
    msgCt.alignTo(document, 't-t');

    var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
    var m = Ext.DomHelper.append(msgCt, {html:this.createBox(title, s)}, true);
    m.slideIn('t').pause(5).ghost("t", {remove:true});

	setTimeout('AgilePHP.Studio.Notification.notes.pop()', 5000);
};
AgilePHP.Studio.Notification.notes = [];
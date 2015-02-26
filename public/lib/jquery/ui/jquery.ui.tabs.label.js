$.extend( $.ui.tabs.prototype, {
	label: function(index, label) {
        if(index >= this.anchors.length)
            return;

        // copy old values for the URL and class names.
        var url=this.anchors.eq(index).attr('href');
        var classes=this.lis[index].className;

        // make a new list item with the same relevant info -
        //     url, classes, but with updated label.
        var $li = $(this.options.tabTemplate
                   .replace(/#\{href\}/g, url)
                   .replace(/#\{label\}/g, label));
        $li.addClass(classes).data('destroy.tabs', true);

        // do the replacement and remove the extra data
        //     attached to the element elsewhere.
        $(this.lis[index]).replaceWith($li)
                   .removeData('cache.tabs')
                   .removeData('load.tabs');

        // cause DOM events to get updated.
        this._tabify();
    }	
});
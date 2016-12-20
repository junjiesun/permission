// JavaScript Document
//===== BEGIN: class Banner =====/
function organizeSelectTree(domSelector, showUser ,multipleSelect) {
  this.domSelector = domSelector;
  this.showUser = showUser == undefined ? true : showUser;
  this.multipleSelect = (multipleSelect == undefined ? true : multipleSelect);
  this.init();
  this.lock = false;
}

organizeSelectTree.prototype.init = function() {
  self = this;
  self.domTree = $('#treeview', this.domSelector);
  self.userModal = $('#userModal', this.domSelector);
  self.orgModal = $('.orgmodal', this.domSelector);
  plugins = ['types','search','checkbox']; //['dnd','types', 'contextmenu'];
  if(!this.showUser){ plugins.splice(2,1);}

  $(self.domTree)
    .jstree({
      'core': {
        "multiple": this.multipleSelect ,
        'data': {
          'url': '/organize/getnode?show='+self.showUser+'&selOrgan=yes&a='+Math.random(6),
          'dataType': 'json',
          'cache':false
        },
        'themes': {
          'responsive': true,
          "icons": true
        },
        'state': false,
        'sort': false
      },
      'force_text': true,
      'plugins': plugins,
      'types' : {
            'default' : {
                'icon' : 'fa fa-folder'
            }
      }
    })
    .on("changed.jstree", function (e, data) {
        //alert(data.selected.length);
        // console.log(data.selected);
    })
    .on('loaded.jstree', function() {
      //$(this).jstree("open_all");
       $(this).jstree("close_all");
       // $(this).jstree(true).select_node('u101_1003');
       // $(this).jstree(true).select_node('u304_1063');
    });

    var to = false;
    $('[name=node]').keyup(function () {
      if(to) { clearTimeout(to); }
      to = setTimeout(function () {
        var v = $('[name=node]').val();
         $(self.domTree).jstree(true).search(v); 
      }, 250);
    });
};

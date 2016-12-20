// JavaScript Document
//===== BEGIN: class Banner =====/
function organizeTree(domSelector, showUser) {
  this.domSelector = domSelector;
  this.showUser = showUser == undefined ? true : showUser;
  this.init();
  this.lock = false;
}

organizeTree.prototype.init = function() {
  self = this;
  self.domTree = $('#treeview', this.domSelector);
  self.userModal = $('#userModal', this.domSelector);
  self.orgModal = $('.orgmodal', this.domSelector);
  plugins = ['dnd','types', 'contextmenu'];
  if(!this.showUser){ plugins.splice(2,1);}
  
  $(self.domTree)
    .jstree({
      'core': {
        'data': {
          'url': '/organize/getnode?show='+self.showUser+'&a='+Math.random(6),
          'dataType': 'json',
          'cache':false
        },
        'check_callback': function(operation, node, node_parent, node_position, more) {
          if(operation == 'move_node' && operation != 'create_node' && node.original.type == 'user')
          {
            return false;
          }

          if (operation != 'create_node' && node.original.id == 0) {
            return false;
          }
          return true;
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
      },
      'contextmenu': {
        'items': {
          "create": {
            "label": "创建",
            '_disabled': function(data) {
              inst = $.jstree.reference(data.reference);
              obj = inst.get_node(data.reference);
              return obj.original.type == 'user' ? true : false;
            },
            "action": function(data) {
              var inst = $.jstree.reference(data.reference),
              obj = inst.get_node(data.reference);
              
// console.log('create action');
// console.log(obj);
              organize_id = obj.id;
              organize_name = obj.text;
              $modal = self.orgModal;
              $('h4', $modal).find('span').html(organize_name);
              //打开之前，自动赋值给code
              self.autoCodeNum();
              $modal.modal('show');
              $modal.on('bs.hidden.modal',function(){
                $('[name=code]',$modal).val('');
                $('[name=name]',$modal).val('');
              })
              $('[name=submit]', $modal).click(function() {
                
                code = $('[name=code]',$modal).val();
                name = $('[name=name]',$modal).val();
                type = $('select[name=type]',$modal).val();

                if(!self.lock){
                  inst.create_node(obj, {code:code, name:name, type:type}, "last", function(new_node) {
                    // setTimeout(function() {inst.edit(new_node);}, 0);
                    // $(self.domTree).jstree('refresh').jstree("open_all");
                  });
                }
              });
            }
          },
          "rename": {
            "label": "重命名",
            '_disabled': function(data) {
              inst = $.jstree.reference(data.reference);
              obj = inst.get_node(data.reference);
              return obj.original.type == 'user' || obj.original.id == 0 ? true : false;
            },
            "action": function(data) {
              var inst = $.jstree.reference(data.reference),
                obj = inst.get_node(data.reference);
              inst.edit(obj);
            }
          },
          "remove": {
            "label": "删除",
            '_disabled': function(data) {
              inst = $.jstree.reference(data.reference);
              obj = inst.get_node(data.reference);
              return obj.original.id == 0 ? true : false;
            },
            "action": function(data) {
              if (confirm('确定要删除么，删除后无法恢复？')) {
                var inst = $.jstree.reference(data.reference),

                  obj = inst.get_node(data.reference);
                if (inst.is_selected(obj)) {
                  inst.delete_node(inst.get_selected());
                } else {
                  inst.delete_node(obj);
                }
                self.countUserNum();
              }
            }
          },
          "user": {
            "label": "添加员工",
            '_disabled': function(data) {
              inst = $.jstree.reference(data.reference);
              obj = inst.get_node(data.reference);
              return obj.original.type == 'user' ? true : false;
            },
            "action": function(nodedata) {
              var inst = $.jstree.reference(nodedata.reference);
              obj = inst.get_node(nodedata.reference);
              organize_id = obj.id;
              organize_name = obj.text;
              $modal = self.userModal;
              $('h4', $modal).find('span').html(organize_name);

              $.getUser.domReady();
              $modal.modal();
              //save organize user
              $('.save-user-group', $modal).click(function() {
                self.organizeUserPost($modal, organize_id, obj);
              });
            }
          },
        }
      }
    })
    .on('delete_node.jstree', function(e, data) {
      $.get('/organize/delnode', {
          'id': data.node.id,
          'type': data.node.original.type,
          'pid': data.node.parent
        })
        .done(function(d) {
          if (d.httpStatusCode != 200) {
            showmodal(d.message);
            data.instance.refresh();
          }
        })
        .fail(function() {
          data.instance.refresh();
        });
    })
    .on('create_node.jstree', function(e, data) {
// console.log('create event');
// console.log(obj);
      code =  data.node.original.code;
      name =  data.node.original.name;
      type =  data.node.original.type;

      // return false;
      if(!code || !name || !type){
        data.instance.refresh();
        alert('请填写完整信息'); return false;
      }
      if(!self.lock){
        self.lock = true;
        $.get('/organize/createnode', {
            'pid': obj.id,
            'original': data.node.original
          })
          .done(function(d) {
            self.lock = false;
            if (d.httpStatusCode == 200) {
              data.instance.set_id(data.node, d.id);
              data.instance.refresh();
              $modal = $(self.orgModal);
              $('[name=code]',$modal).val('');
              $('[name=name]',$modal).val('');
              $modal.modal('hide');
              $(self.domTree).jstree(true).refresh_node(obj);

            } else {
              alert(d.message);
              $(self.domTree).jstree(true).refresh();
            }
          })
          .fail(function() {
            data.instance.refresh();
            $(self.domTree).jstree(true).refresh();
          });
      }
      
    })
    .on('rename_node.jstree', function(e, data) {
      $.get('/organize/edit', {
          'id': data.node.id,
          'name': data.text
        })
        .done(function(d) {
          if (d.httpStatusCode != 200) {
            // showmodal(d.message);
            data.instance.refresh();
            $(self.domTree).jstree(true).refresh();
          }
        })
        .fail(function() {
          data.instance.refresh();
        });
    })
    .on('move_node.jstree', function(e, data) {
      $.get('/organize/edit?operation=move', {
          'id': data.node.id,
          'parent': data.parent,
          'position': data.position
        })
        .done(function(d) {
          if (d.httpStatusCode != 200) {
            // showmodal(d.message);
            data.instance.refresh();
            $(self.domTree).jstree(true).refresh();
          }
        })
        .fail(function() {
          data.instance.refresh();
        });
    })
    .on('copy_node.jstree', function(e, data) {
      showmodal('该功能不可用');
      data.instance.refresh();
      return false;
    }).on('loaded.jstree', function() {
      $(this).jstree("open_all");
      self.countUserNum();
    }).on('refresh_node.jstree', function() {
        self.countUserNum();
    }).on('refresh.jstree', function() {
        self.countUserNum();
    });

    var to = false;
    $('[name=node]').keyup(function () {
      if(to) { clearTimeout(to); }
      to = setTimeout(function () {
        var v = $('[name=node]').val();
         $(self.domTree).jstree(true).search(v); 
      }, 250);
    });
    // .on('changed.jstree', function (e, data) {
    //   if(data && data.selected && data.selected.length) {
    //     if(data.node.original.type == 'user')
    //     { 
    //       id = data.node.a_attr.id;
    //       $('#'+id).contextmenu(function(e){
    //          return false;
    //       })
    //     }
    //   }
    // })
    // .on('changed.jstree', function (e, data) {
    //   if(data && data.selected && data.selected.length) {
    //     $.get('/organize/getnode?id=' + data.selected.join(':'), function (d) {

  //       $('#data .default').text(d.content).show();
  //     });
  //   }
  //   else {
  //     $('#data .content').hide();
  //     $('#data .default').text('Select a file from the tree.').show();
  //   }
  // })

  // bind jstree.event to func
  // $(this.domSelector).bind("get_node.jstree", function (event, data) {

  // }); 
};

organizeTree.prototype.organizeUserPost = function(ui, oid, obj) {
  self = this;
  uids = $('#select-user-list', ui).val();
  if (uids == null) {
    return false;
  }
  if (!self.lock) {
    self.lock = true;
    $.ajax({
      url: '/organize/adduser',
      data: {
        uids: uids,
        oid: oid
      },
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        self.lock = false;
        if (response.httpStatusCode == 200) {
          $(ui).modal('hide');
          // showmodal('操作成功');
          $(self.domTree).jstree(true).refresh_node(obj);
        } else {
          showmodal(response.message);
        }
      }
    });
  }
}

// 计算人员数量
organizeTree.prototype.countUserNum = function() {
  $(self.domTree).find("li[role=treeitem]").each(function(index,element) {
    if($(this).attr('id').substr(0,1)=='u'){
      return;
    }
    var userNmu = $('a.jstree-anchor[id^=u]',$(this)).length;
    var deparetLi = $('a.jstree-anchor:first',$(this));

    var oldText = deparetLi.text();
    var temIdx = oldText.indexOf(' (');
    if(temIdx > 0){oldText = (oldText.substr(0,temIdx)) ;}

    deparetLi.text(oldText+' ('+userNmu+')');
  });
};

organizeTree.prototype.autoCodeNum = function() {
  if (!self.lock) {
    self.lock = true;
    $.ajax({
      url: '/organize/getnodeid',
      data: {},
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        self.lock = false;
        if (response.httpStatusCode == 200) {
           $('[name=code]',this.orgModal).val(response.message);
        }
      }
    });
  }
};

// JavaScript Document
//===== BEGIN: class Banner =====/
function organizeTree(domSelector, showUser) {
  this.domSelector = domSelector;
  this.showUser = showUser == undefined ? true : showUser;
  this.init();
}

organizeTree.prototype.init = function() {
  self = this;
  self.domTree = $('#treeview', this.domSelector);
  self.userModal = $('#userModal', this.domSelector);
  
  plugins = ['dnd', 'contextmenu', 'wholerow','search'];
  if(!this.showUser){ plugins.splice(1,1);}
  
  $(self.domTree)
    .jstree({
      'core': {
        'data': {
          'url': '/organize/getnode?show='+self.showUser,
          'dataType': 'json'
        },
        'check_callback': function(operation, node, node_parent, node_position, more) {
          // operation can be 'create_node', 'rename_node', 'delete_node', 'move_node' or 'copy_node'
          // in case of 'rename_node' node_position is filled with the new node name
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
        'sort': true
      },
      'force_text': true,
      'plugins': plugins,
      'contextmenu': {
        'items': {
          "create": {
            "label": "创建",
            '_disabled': function(data) {
              inst = $.jstree.reference(data.reference);
              obj = inst.get_node(data.reference);
              return obj.original.type == 'user' ? true : false;
              //return !$.jstree.reference(data.reference).can_paste();
            },
            "action": function(data) {
              var inst = $.jstree.reference(data.reference),
                obj = inst.get_node(data.reference);
              inst.create_node(obj, {}, "last", function(new_node) {
                setTimeout(function() {
                  inst.edit(new_node);
                }, 0);
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
              }
            }
          },
          "ccp": {
            "label": "编辑",
            '_disabled': function(data) {
              inst = $.jstree.reference(data.reference);
              obj = inst.get_node(data.reference);
              return obj.original.type == 'user' || obj.original.id == 0 ? true : false;
            },
            "action": false,
            "submenu": {
              "cut": {
                "label": "剪切",
                '_disabled': false,
                "action": function(data) {
                  var inst = $.jstree.reference(data.reference),
                    obj = inst.get_node(data.reference);
                  if (inst.is_selected(obj)) {
                    inst.cut(inst.get_top_selected());
                  } else {
                    inst.cut(obj);
                  }
                }
              },
              "copy": {
                "label": "复制",
                '_disabled': false,
                "action": function(data) {
                  var inst = $.jstree.reference(data.reference),
                    obj = inst.get_node(data.reference);
                  if (inst.is_selected(obj)) {
                    inst.copy(inst.get_top_selected());
                  } else {
                    inst.copy(obj);
                  }
                }
              },
              "paste": {
                "_disabled": function(data) {
                  return !$.jstree.reference(data.reference).can_paste();
                },
                "separator_after": false,
                "label": "粘贴",
                "action": function(data) {
                  var inst = $.jstree.reference(data.reference),
                    obj = inst.get_node(data.reference);
                  inst.paste(obj);
                }
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
                self.organizeUserPost($modal, organize_id);
                $(self.domTree).jstree('refresh');
                setTimeout(function() {
                  window.location.reload();
                }, 1000);
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
      $.get('/organize/createnode', {
          'pid': data.node.parent,
          'name': data.text
        })
        .done(function(d) {
          if (d.httpStatusCode == 200) {
            data.instance.set_id(data.node, d.id);
          } else {
            showmodal(d.message);
            data.instance.refresh();
          }
        })
        .fail(function() {
          data.instance.refresh();
        });
    })
    .on('rename_node.jstree', function(e, data) {
      $.get('/organize/edit', {
          'id': data.node.id,
          'name': data.text
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
    .on('move_node.jstree', function(e, data) {
      $.get('/organize/edit?operation=move', {
          'id': data.node.id,
          'parent': data.parent,
          'position': data.position
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
    .on('copy_node.jstree', function(e, data) {
      showmodal('该功能不可用');
      data.instance.refresh();
      return false;
    }).on('loaded.jstree', function() {
      $(this).jstree("open_all");
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

organizeTree.prototype.organizeUserPost = function(ui, oid) {
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
          showmodal('操作成功');
        } else {
          showmodal(response.message);
        }
      }
    });
  }
}
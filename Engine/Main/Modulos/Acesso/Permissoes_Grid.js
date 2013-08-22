/**
 * @class   :Permissoes_Grid.js
 * @author  :Glauber Costa Vila-Verde
 * @date    :20/03/2010
 */

// Permissoes_Grid
// 'main.php'
// , tela : 'manutencao_permissoes'
// 'id'
var Permissoes_Grid = Ext.extend(Ext.grid.EditorGridPanel,{

    //Config Options
    border      : false
    , id        : 'Permissoes_Grid'
    , stripeRows: true
    , loadMask  : true
    , width     : '100%'
    , viewConfig: {
        forceFit:true
    }
    // Atributos de controle de Permissoes
    , perm_insert: 1
    , perm_delete: 1
    , perm_update: 1
    , perm_execut: 1

    , setPermissaoUpd: function(perm_update) {
        this.perm_update = perm_update;
    }

    // inits
    , initComponent: function() {

        //store do grid
        this.store =  new Ext.data.JsonStore({
            url             : 'main.php'
            , root          : 'rows'
            , idProperty    : 'id'
            , totalProperty : 'totalCount'
            , autoDestroy   : true
            , baseParams    : {
                classe: 'Permissoes'
                , action: 'get_todas_permissoes'
            }
            , fields:[
                {name:'id'        , type:'int'     , mapping: 'id'} // id das permissoes
                , {name:'Usuario' , type:'string'  , mapping: 'Usuario' }
                , {name:'Root'    , type:'string'  , mapping: 'Root'}
                , {name:'Tela'    , type:'string'  , mapping: 'Tela'}
                , {name:'Titulo'  , type:'string'  , mapping: 'Titulo'}
                , {name:'Descricao' , type:'string', mapping: 'Descricao'}
                , {name:'sel'     , type:'bool'    , mapping: 'sel'}
                , {name:'ins'     , type:'bool'    , mapping: 'ins'}
                , {name:'upd'     , type:'bool'    , mapping: 'upd'}
                , {name:'del'     , type:'bool'    , mapping: 'del'}
                , {name:'imp'     , type:'bool'    , mapping: 'imp'}
                , {name:'exc'     , type:'bool'    , mapping: 'exc'}
            ]
        });

        // Store da ComboBox Usuario
        this.cmbGrupoUsuario = new Ext.form.ComboBox({
            root             : 'rows'
            , triggerAction  : 'all'
            , valueField     : 'id'
            , displayField   : 'Grupo'
            , emptyText      : 'Selecione um Grupo'
            , width          : 250
            , allowBlank     : false
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'id'
                , autoDestroy : true
                , baseParams  : {
                    classe  : 'GruposUsuarios'
                    , action : 'getGrupos'
                }
                , fields:[
                    {name:'id'      , type:'int'}
                    , {name:'Grupo' , type:'string'}
                    , {name:'Descricao'  , type:'string'}
                ]
            })
            , listeners:{
                select:function(combo){
                    Ext.getCmp('Permissoes_Grid').getStore().load({params:{id_Usuario:combo.getValue()}})
                }
            }
        })

       // Store de Categorias
        var storeCategorias = new Ext.data.JsonStore({
                url              : 'main.php'
                , root           : 'rows'
                , idProperty     : 'id'
                , autoLoad       : true
                , autoDestroy    : true
                , baseParams     : {
                    classe  : 'Permissoes'
                    , action : 'get_todas_categorias'
                }
                , fields:[
                    {name:'id'       , type:'int'}
                    , {name:'titulo' , type:'string'}
                ]
            });
        storeCategorias.load();

        this.Renderers = {
            categoria : function(value) {
                record = storeCategorias.getById(value);
                if(record) {
                    return record.data.titulo;
                } else {
                    return 'Nenhum Registro encontrado';
                }
            }
        };

        //demais atributos do grid
        Ext.apply(this,{
            viewConfig:{
                emptyText        : 'Nenhum registro encontrado, Selecione um Usu&aacute;rio.'
                , forceFit       :true
                , deferEmptyText : false
            }
            , bbar: new Ext.PagingToolbar({ //paginação
                store        : this.store
                , pageSize   : 30
                , displayInfo: true
                , displayMsg : 'Mostrando resultados {0} - {1} de {2}'
                , plugins: new Ext.ux.ProgressBarPager()
            })
            , tbar:['Grupo de Usu&aacute;rio:',' ', this.cmbGrupoUsuario
                ,' ', '-', ' ', this.btnSalvar = new Ext.Button({
                    text     : 'Salvar' // botão para Salvar as alterações
                    , id     : 'btnSalvar'
                    , iconCls: 'silk-disk'
                    , scope  : this
                    , handler: this._onBtnSalvarClick
                    , tooltip: {title:'Salvar Alterações',text:'Click para Salvar as informações sobre Permissões de Usu&aacute;rio.'}
                })]
            , columns:[{
                dataIndex   : 'id'
                , header    : 'Código'
                , width     : 50
                , hidden    : true
            },{
                dataIndex   : 'Root'
                , header    : 'Categoria'
                , width     : 100
                , renderer  : this.Renderers.categoria
                , sortable  : true
            },{
                dataIndex   : 'Titulo'
                , header    : 'Entidade'
                , width     : 100
                , sortable  : true
            },{
                dataIndex   : 'Descricao'
                , header    : 'Descrição'
                , width     : 100
                , sortable  : true
            },{
                dataIndex   : 'sel'
                , header    : 'Visualizar'
                , width     : 40
                , checkbox  :true
                , editor    :true
            },{
                dataIndex   : 'ins'
                , header    : 'Inserir'
                , width     : 40
                , checkbox  :true
                , editor    :true
            },{
                dataIndex   : 'upd'
                , header    : 'Alterar'
                , width     : 40
                , checkbox  :true
                , editor    :true
            },{
                dataIndex   : 'del'
                , header    : 'Excluir'
                , width     : 40
                , checkbox  :true
                , editor    :true
            },{
                dataIndex   : 'imp'
                , header    : 'Imprimir'
                , width     : 40
                , checkbox  :true
                , editor    :true
            },{
                dataIndex   : 'exc'
                , header    : 'Executar'
                , width     : 40
                , checkbox  :true
                , editor    :true
            }]
            , collapsible: true
            , animCollapse: false
            , clicksToEdit:1
        })
        // Super
        Permissoes_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        // super
        Permissoes_Grid.superclass.initEvents.call(this);

        this.on('afterrender', this._onAfterRender, this);
    }

    //Overrides
    , onDestroy: function() {
        Permissoes_Grid.superclass.onDestroy.apply(this,arguments);
    }

    // Listeners
    , _onAfterRender: function() {
        var perm_insert;
        var perm_update;
        var perm_delete;
        var perm_execut;

        //REQUISIÇÃO EM AJAX
        Ext.Ajax.request({
            url : 'main.php'
            , method: 'POST'
            , scope: this
            , params : {
                classe : 'Permissoes'
                , action  : 'retorna_permissoes'
                , tela : 'manutencao_permissoes'
            }
            , success: function ( result, request ) {
                var data = Ext.decode(result.responseText);
                perm_insert = data.ins;
                perm_delete = data.del;
                perm_execut = data.exc;
                perm_update = data.upd;

                // Habilitando/Desabilitando os Botões
                if (perm_insert == 0) {
                    Ext.getCmp('btnSalvar').disable();
                }
                if (perm_update == 0) {
                    Ext.getCmp('btnSalvar').disable();
                }

            }
        });
    }

    // Listener disparado ao clicar no botão "Salvar"
    , _onBtnSalvarClick: function() {

        var mod = [];
        var store = this.store;
        //Pega só os dados de cada registro modificado
        Ext.each(this.store.getModifiedRecords(), function(e){
            mod.push(e.data);
        });

        Ext.getCmp('main_statusbar').msg('saving');

        Ext.Ajax.request({
            url: 'main.php',
            params:{
                classe : 'Permissoes'
                , action: 'CriaAtualizaPermissoes'
                //Manda uma var chamada dados com o json dos registros
                , dados: Ext.encode(mod)
            }
            , success: function( r, o ){
                var obj = Ext.decode(r.responseText);
                if(obj.success){
                    //Marca os registros como salvos
                    store.commitChanges();
                    Ext.Msg.alert('Status', 'Registro(s) atualizado(s) com sucesso!');
                    Ext.getCmp('main_statusbar').msg('save');
                    // Reload no Store Global de Permissoes
                    permissoes_store.load();
                }
                else {
                    store.rejectChanges();
                    Ext.MessageBox.show({ title:'Status', msg: '', buttons: Ext. MessageBox.OK, icon: Ext.MessageBox.WARNING });
                }
            }
        })
    }
    // Demais métodos

});

Ext.reg('e-Permissoes_Grid', Permissoes_Grid);
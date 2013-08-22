/**
 * @package     : Emporium
 * @name        : Integracao_Emporium_Grid
 * @version     : 1.0
 * @Diretorio   : Main/Modulos/Emporium/
 * @Dependencias: Integracao_Emporium_Form
 * @date        : 09/12/2010
 */

var Integracao_Emporium_Grid = Ext.extend(Ext.grid.GridPanel,{

    $depends     : ['Integracao_Emporium_Form.js']
    , id         : 'Integracao_Emporium_Grid'
    , identificacao : '6001' // Identificacao para permissoes

    , main_url   : 'main.php'
    , main_class : 'Integracao_Emporium'
    , pk_id      : 'pk_id_integracao'
    , metodo_load: 'load_integracoes'
    , metodo_delete: 'delete_integracoes'

    , border     : false
    , stripeRows : true
    , loadMask   : true

    // Atributos de controle de Permissoes
    // 0 Desabilitado / 1 Habilitado
    , perm_insert: 0
    , perm_delete: 0
    , perm_update: 0
    , perm_execut: 0
    , perm_impres: 0

    , constructor: function() {
        // Tratamento de Permissoes
        indice = permissoes_store.findExact('identificacao', this.identificacao);
        record = permissoes_store.getAt(indice);
        this.perm_insert = record.data.ins;
        this.perm_delete = record.data.del;
        this.perm_update = record.data.upd;
        this.perm_execut = record.data.exc;
        this.perm_impres = record.data.imp;

        Integracao_Emporium_Grid.superclass.constructor.apply(this,arguments);
    }

    , setPermissaoUpd: function(perm_update) {
        this.perm_update = perm_update;
    }

    ,initComponent: function() {
        //store do grid
        this.store = new Ext.data.JsonStore({
            url            : this.main_url
            , root           : 'rows'
            , idProperty     : this.pk_id
            , totalProperty  : 'totalCount'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : this.main_class
                , action : this.metodo_load
                , limit  : 30
            }
            , fields:[
                {name:this.pk_id       ,  type:'int'}
                , {name:'fk_id_usuario',  type:'int'}
                , {name:'tipo'         ,  type:'int'}
                , {name:'entidade'     ,  type:'string'}
                , {name:'dt_inicio'    ,  type:'string'}
                , {name:'dt_termino'   ,  type:'string'}
                , {name:'total'        ,  type:'int'}
                , {name:'total_erros'  ,  type:'int'}
                , {name:'status'       ,  type:'string'}
                , {name:'arquivo'      ,  type:'string'}
            ]
        });


        // Store de Status
        var storeStatus = new Ext.data.JsonStore({
            url              : 'main.php'
            , root           : 'rows'
            , idProperty     : 'pk_id_status'
//            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'Common'
                , action : 'getStatus'
            }
            , fields:[
                {name:'pk_id_status' , type:'int'}
                , {name:'status'     , type:'string'}
            ]
        });
        storeStatus.load();

        this.Renderers = {
            status : function(value) {
                record = storeStatus.getById(value);
                if(record) {
                    return record.data.status;
                } else {
                    return 'Indefinido';
                }
            }
            , tipo : function(value) {
                if(value == 1) {
                    return 'Importação'
                }
                else if(value == 2){
                    return 'Exportação'
                }
                else {
                    return 'Indefinido';
                }
            }
        };


        this.sm = new Ext.grid.CheckboxSelectionModel();

        //demais atributos do grid
        Ext.apply(this,{
            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , forceFit:true
                , deferEmptyText : false
            }
            , bbar: new Ext.PagingToolbar({
                store      : this.store
                , pageSize  : 30
                , displayInfo: true
                , displayMsg: 'Mostrando resultados {0} - {1} de {2}'
                , plugins: new Ext.ux.ProgressBarPager()

            })
            ,tbar: [{
                text     : 'Excluir'
                , id     : 'btnExcluir'
                , iconCls: 'silk-delete'
                , tooltip: {title:'Excluir Selecionados',text:'Selecione uma ou mais linhas para excluir os registros. <b>Dica:</b> Para selecionar mais de uma linha segure a tecla "Crtl"'}
                , scope  : this
                , handler: this._onBtnExcluirSelecionadosClick
            }]
            , columns:[
            new Ext.grid.RowNumberer()
            , this.sm
            , {
                dataIndex   : this.pk_id
                , header    : 'C&oacute;digo'
                , width     : 50
                , sortable  : true
            },{
                dataIndex   : 'tipo'
                , header    : 'Tipo'
                , width     : 100
                , sortable  : true
                , renderer  : this.Renderers.tipo
            },{
                dataIndex   : 'entidade'
                , header    : 'Entidade'
                , width     : 100
                //, sortable  : true
            },{
                dataIndex   : 'dt_inicio'
                , header    : 'Inicio'
                , width     : 100
                , sortable  : true
            },{
                dataIndex   : 'dt_termino'
                , header    : 'Termino'
                , width     : 100
                , sortable  : true
            },{
                dataIndex   : 'status'
                , header    : 'Status'
                , width     : 100
                , sortable  : true
                , renderer  : this.Renderers.status
            }]
            , sm: this.sm
            , plugins:new Ext.ux.grid.Search({
                readonlyIndexes:[this.pk_id]
                , disableIndexes:[this.pk_id]
                , position: 'top'
                , mode: 'remote'
                , width: 200
            })
        })

        Integracao_Emporium_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Integracao_Emporium_Grid.superclass.initEvents.call(this);
        // depois de renderizado carrega as permissoes
        this.on('afterrender', this._onAfterRender, this);

        // Ao clicar em linha da Grid abrir janela para edicao
        this.on({
            scope      : this
            , rowdblclick: this._onGridRowDblClick
        });
    }


    //Overrides

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Integracao_Emporium_Grid.superclass.onDestroy.apply(this,arguments);
        //destroi a janela
        Ext.destroy(this._winForm)
        this._winForm = null;
    }

    //Listeners

    // Listener disparado ao clicar no botao "Excluir Selecionados"
    , _onBtnExcluirSelecionadosClick: function() {
        //busco selecionados
        var arrSelecionados = this.getSelectionModel().getSelections();

        if( arrSelecionados.length === 0 ) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Selecione ao menos um registro!')
            return false;
        }
        else {
            Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo excluir o(s) registro(s) selecionado(s)?',function(opt){

                if(opt === 'no') {
                    return;
                }
                else {
                    var ids = [];
                    for( var i = 0 ; i < arrSelecionados.length ; i++ ) {
                        ids.push( arrSelecionados[i].get(this.pk_id) );
                    }

                    this.el.mask('Excluindo Registros');
                    Ext.getCmp('main_statusbar').msg('job');

                    Ext.Ajax.request({
                        url    : this.main_url
                        , params : {
                            classe : this.main_class
                            , action: this.metodo_delete
                            , 'pk_id_integracao[]': ids
                        }
                        ,scope  : this
                        ,success: function() {
                            this.el.unmask();
                            this.store.removeAll();
                            this.store.reload();
                            Ext.getCmp('main_statusbar').msg('ok');
                        }
                    });
                }
            },this);
        }
    }

    // Listener disparado ao clicar em alguma linha do grid
    , _onGridRowDblClick: function( grid, rowIndex, e ){
        //busca registro da linha selecionada
        var record = grid.getStore().getAt(rowIndex);

        //extrai id
        var registroID = record.get(this.pk_id);

        //cria janela de cadastro
        this._criaWindowForm();

        //seta atributos
        this._winForm.setRegistroID(registroID);
        //mostra
        this._winForm.show();
    }

    , _onWinFormSalvarExcluir: function() {
        //recarrega grid
        this.store.removeAll();
        this.store.reload();
    }

    //Demais metodos

    //Metodo para criar a janela de cadastro de usuario
    , _criaWindowForm: function() {
        /*
        * reutilizar a janela. Para isso so criamos ela se a sua referencia nao existe ainda.
        * definir o config. option closeAction:'hide' na janela, para que ela nao seja destruida.
        */
        if(!this._winForm) {
            this._winForm = new Integracao_Emporium_Form({
                //renderTo   : this.body
                listeners  :{
                    scope  : this
                    , salvar : this._onWinFormSalvarExcluir
                    , excluir: this._onWinFormSalvarExcluir
                }
            });
        }

        return this._winForm;
    }

    // Controle de Permissoes
    , _onAfterRender: function() {

        if (this.perm_insert == 0) {
            Ext.getCmp('btnNovo').setVisible(false);
        }
        if (this.perm_delete == 0) {
            Ext.getCmp('btnExcluir').setVisible(false);
        }
        if (this.perm_execut == 0) {

        }
        if (this.perm_update == 0) {

        }
        if (this.perm_impres == 0) {

        }
    }


});

Ext.reg('e-Integracao_Emporium_Grid', Integracao_Emporium_Grid);




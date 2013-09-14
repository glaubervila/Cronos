/**
 * @package     : Orcamentos
 * @name        : Orcamentos_Servidor_Grid
 * @Diretorio   : Main/Modulos/Orcamentos/
 * @Dependencias:
 * @date        : 21/11/2011
 * @version     : 1.0
 */

var Orcamentos_Servidor_Grid = Ext.extend(Ext.grid.GridPanel,{

    $depends     : ['Orcamentos_Servidor_Form.js']
    , id         : 'Orcamentos_Servidor_Grid'
    , identificacao : '7001' // Identificacao para permissoes

    , main_url   : 'main.php'
    , main_class : 'Orcamentos_Servidor'
    , pk_id      : 'pk_orcamento'
    , metodo_load: 'getOrcamentos'
    , metodo_delete: 'delete_Orcametos'

    , border     : false
    , stripeRows : true
    , loadMask   : true

    // Atributos de controle de Permissoes
    // 0 Desabilitado / 1 Habilitado
    , perm_insert: 1
    , perm_delete: 1
    , perm_update: 1
    , perm_execut: 1
    , perm_impres: 1

    , constructor: function() {
        // Tratamento de Permissoes
        indice = permissoes_store.findExact('identificacao', this.identificacao);
        record = permissoes_store.getAt(indice);
        this.perm_insert = record.data.ins;
        this.perm_delete = record.data.del;
        this.perm_update = record.data.upd;
        this.perm_execut = record.data.exc;
        this.perm_impres = record.data.imp;

        Orcamentos_Servidor_Grid.superclass.constructor.apply(this,arguments);
    }

    ,initComponent: function() {

        // Store de Status
//         var storeStatus = new Ext.data.SimpleStore({
//             idProperty     : 'id'
//             , fields:[
//                 {name:'id'       , type:'int'}
//                 , {name:'status' , type:'string'}
//                 , {name:'cor'    , type:'string'}
//             ]
//             , data: [
//                   [0, 'Aberto', '0000FF']
//                 , [1, 'Fechado','00FF00']
//                 , [2, 'Cancelado','FF0000']
//                 , [3, 'À Separar','FF6600']
//                 , [4, 'Aguardando Pagamento','008000']
//                 , [5, 'Em Entrega','333399']
//                 , [6, 'Finalizado','969696']
//             ]
//         });
        var storeStatus = new Ext.data.JsonStore({
            url            : this.main_url
            , root           : 'rows'
            , idProperty     : 'id'
            , totalProperty  : 'totalCount'
            , autoLoad       : true
            , autoDestroy    : true
            //, mode           : 'local'
            , baseParams     : {
                classe  : this.main_class
                , action : 'getStoreStatus'
            }
            , fields:[
                  {name:'id'            ,  type:'string'}
                , {name:'status'        ,  type:'string'}
                , {name:'cor'           ,  type:'string'}
            ]
        });
        storeStatus.load();


        //store do grid
        this.store = new Ext.data.JsonStore({
            url            : this.main_url
            , root           : 'rows'
            , idProperty     : this.pk_id
            , totalProperty  : 'totalCount'
            //, autoLoad       : true
            , autoDestroy    : true
            , remoteSort     : true
            , baseParams     : {
                classe  : this.main_class
                , action : this.metodo_load
                , limit  : 30
            }
            , fields:[
                {name:this.pk_id              , type:'string'}
                , {name:'cliente'             ,  type:'string'}
                , {name:'qtd_itens'           ,  type:'string'}
                , {name:'dt_inclusao'         ,  type: 'date', dateFormat: 'Y-m-d H:i:s'}
                , {name:'status_nome'         ,  type:'string'}
                , {name:'status'              ,  type:'int'}
                , {name:'status_servidor_nome',  type:'string'}
                , {name:'vendedor'            ,  type:'string'}
                , {name: 'action_novo'        , type: 'string'}
            ]
        });

        //campo para ordenação default
        this.store.setDefaultSort('dt_inclusao', 'DESC');
        this.store.load();

        this.action = new Ext.ux.grid.RowActions({
            header:''
            ,autoWidth:false
            , width:30
            //,hideMode:'display'
            ,keepSelection:true
            ,actions:[{
                iconIndex:'action_novo'
                //qtipIndex:'qtip1'
                //iconCls:'silk-novo-gif'
                ,tooltip:'Orcamento Novo!!!'
            }]
            , callbacks:[{
                'silk-novo-gif':function(grid, record, action, row, col) {
                    Ext.ux.Toast.msg('Callback: icon-plus', 'You have clicked row: <b>{0}</b>, action: <b>{0}</b>', row, action);
                },
            }]
        });

        this.action.on({
            action:function(grid, record, action, row, col) {
                //Ext.ux.Toast.msg('Event: action', 'You have clicked row: <b>{0}</b>, action: <b>{1}</b>', row, action);
                switch (action) {
                    // Botao Excluir
                    case 'silk-novo-gif':
                        //grid.action_excluir(grid, row, record);
                    break;
                }
            }
            , beforeaction:function() {
                //Ext.ux.Toast.msg('Event: beforeaction', 'You can cancel the action by returning false from this event handler.');
            }
        });



        this.Renderers = {
            status : function(value) {
                index = storeStatus.find('id',value);
                var record = storeStatus.getAt(index);
                if(record) {
                    text = '<font color="#'+record.data.cor+'"><b>'+record.data.status+'</b></font>';
                    return text;
                } else {
                    return 'Nenhum Registro encontrado';
                }
            }
        };


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
                text     : 'Novo'
                , id     : 'orcamentos_servidor_btnNovo'
                , iconCls: 'silk-add'
                , scope  : this
                , handler: this._onBtnNovoOrcamentoClick
                , tooltip: {title:'Criar Novo Registro',text:'Click para adicionar um novo registro'}
            }]
            , columns:[
            new Ext.grid.RowNumberer()
            , this.action
            ,{
                dataIndex   : 'status'
                , header    : 'Status'
                , width     : 60
                , sortable  : false
                , renderer  : this.Renderers.status
            },{
                dataIndex   : 'dt_inclusao'
                , header    : 'Data/Hora'
                , xtype     : 'datecolumn'
                , format    : 'd-m-Y H:i:s'
                , renderer : Ext.util.Format.dateRenderer('d-m-Y H:i:s')
                , width     : 70
                , sortable  : true
            },{
                dataIndex   : this.pk_id
                , header    : 'C&oacute;digo'
                , width     : 50
                , sortable  : true
            },{
                dataIndex   : 'vendedor'
                , header    : 'Vendedor'
                , sortable  : false
            },{
                dataIndex   : 'cliente'
                , header    : 'Cliente'
                , sortable  : true
            }]
            , plugins:[this.action]
            /*, plugins:new Ext.ux.grid.Search({
                readonlyIndexes:[this.pk_id]
                //, disableIndexes:[this.pk_id]
                , position: 'top'
                , mode: 'remote'
                , width: 200
            })*/
        })

        Orcamentos_Servidor_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Orcamentos_Servidor_Grid.superclass.initEvents.call(this);
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
        Orcamentos_Servidor_Grid.superclass.onDestroy.apply(this,arguments);
        //destroi a janela
        Ext.destroy(this._winForm)
        this._winForm = null;
    }

    //Listeners

    // Listener disparado ao clicar no botao "Novo"
    , _onBtnNovoUsuarioClick: function() {
        //cria janela de cadastro
        this._criaWindowForm();

        //seta atributos
        this._winForm.setRegistroID(0);

        //mostra
        this._winForm.show();
    }

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
                            , 'pk_orcamento[]': ids
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

        this._winForm.hide();
        this._winForm.show();
    }

    //Demais metodos

    //Metodo para criar a janela de cadastro orcamento
    , _criaWindowForm: function() {

        if (!this._winForm) {
            this._winForm = new Orcamentos_Servidor_Form({
                //renderTo   : this.body
                listeners  :{
                    scope  : this
                    , salvar : this._onWinFormSalvarExcluir
                    , excluir: this._onWinFormSalvarExcluir
                    , alterar_status: this._onWinFormSalvarExcluir
                    , novo_orcamento: this._onWinFormSalvarExcluir
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

    // Listener disparado ao clicar no botao "Novo Orcamento"
    , _onBtnNovoOrcamentoClick: function() {
        //cria janela de Localizar
        this._criaWindowForm();

        //seta atributos
        this._winForm.setRegistroID(0);
        this._winForm.setClienteId(0);

        //mostra
        this._winForm.show();
    }

});

Ext.reg('e-Orcamentos_Servidor_Grid', Orcamentos_Servidor_Grid);
/**
 *
 */
var InformacoesServidor_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'InformacoesServidor_Grid'
    , title : 'Informa&ccedil;&otilde;es do Servidor'
    , width : 400
    , height: 200
    , iconCls:'silk-server_chart'
    , loadMask: true

    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url            : 'main.php'
            , root           : 'rows'
            , idProperty     : 'id'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'ServerStatus'
                , action : 'getInfSistema'
            }
            , fields:[
                {name:'id'      , type:'int'}
                , {name:'nome'  , type:'string'}
                , {name:'valor' , type:'string'}
            ]
        });

        Ext.apply(this,{
            viewConfig:{
                emptyText        : 'Nenhum registro encontrado'
                , forceFit       :true
                , deferEmptyText : false
            }
            , defaults: {
                sortable      : false
                , menuDisabled: true
                , hideable    : false
                , groupable   : false
            }
            , columns:[{
                dataIndex   : 'nome'
                , width     : 80
                },{
                dataIndex   : 'valor'
                }]
        })
        InformacoesServidor_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        InformacoesServidor_Grid.superclass.initEvents.call(this);
    }

    , _onRefresh: function(){
        //Ext.Msg.alert('<font color=red>Debug Message!</font>', this.uptime);
        this.store.load();
    }
});
Ext.reg('e-InformacoesServidor_Grid', InformacoesServidor_Grid);


//=======================================================================================================
/**
 *
 */
var UtilizacaoRede_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'InterfacesRede_Grid'
    , title : 'Utiliza&ccedil;&atilde;o da Rede'
    , width : 400
    , height: 200
    , iconCls:'silk-chart-organisation'
    , loadMask: true

    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url            : 'main.php'
            , root           : 'rows'
            , idProperty     : 'id'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'ServerStatus'
                , action : 'getInterfacesRede'
            }
            , fields:[
                {name:'id'        , type:'int'}
                , {name:'dev_name', type:'string'}
                , {name:'transfer', type:'string'}
                , {name:'receive' , type:'string'}
                , {name:'errs'    , type:'string'}
            ]
        });

        Ext.apply(this,{
            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , forceFit:true
                , deferEmptyText : false
            }
            , defaults: {
                sortable      : false
                , menuDisabled: true
                , hideable    : false
                , groupable   : false
            }
            , columns:[{
                dataIndex   : 'dev_name'
                , header    : 'Interface'
                },{
                dataIndex   : 'transfer'
                , header    : 'Enviados'
                },{
                dataIndex   : 'receive'
                , header    : 'Recebidos'
                },{
                dataIndex   : 'errs'
                , header    : 'Perdidos'
                }]
        })
        UtilizacaoRede_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        UtilizacaoRede_Grid.superclass.initEvents.call(this);
    }

    , _onRefresh: function(){
        //Ext.Msg.alert('<font color=red>Debug Message!</font>', this.uptime);
        this.store.load();
    }
});
Ext.reg('e-UtilizacaoRede_Grid', UtilizacaoRede_Grid);
//=======================================================================================================
/**
 *
 */
var UtilizacaoMemoria_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'UtilizacaoMemoria_Grid'
    , title : 'Utiliza&ccedil;&atilde;o de Mem&oacute;ria'
    , width : 600
    , height: 200
    , iconCls: 'silk-lightning'
    , loadMask: true

    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url            : 'main.php'
            , root           : 'rows'
            , idProperty     : 'id'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'ServerStatus'
                , action : 'getUtilizacaoMemoria'
            }
            , fields:[
                {name:'id'         , type:'int'}
                , {name:'memoria'  , type:'string'}
                , {name:'percent'  , type:'string'}
                , {name:'livre'    , type:'string'}
                , {name:'utilizada', type:'string'}
                , {name:'total'    , type:'string'}
            ]
        });

        Ext.apply(this,{

            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , forceFit:true
                , deferEmptyText : false
            }
            , defaults: {
                sortable      : false
                , menuDisabled: true
                , hideable    : false
                , groupable   : false
            }
            , columns:[{
                    dataIndex: 'memoria'
                    , header : 'Mem&oacute;ria'
                    , width  : 150
                },new Ext.ux.ProgressColumn({
                    header: '% Utilizado'
                    , width: 200
                    , dataIndex: 'percent'
                    , align: 'center'
                    , renderer: function(value, meta, record, rowIndex, colIndex, store, pct) {
                        return Ext.util.Format.number(pct, "0.00%");
                    }
                }),{
                    dataIndex   : 'livre'
                    , header    : 'Livre'
                },{
                    dataIndex   : 'utilizada'
                    , header    : 'Utilizado'
                },{
                    dataIndex   : 'total'
                    , header    : 'Total'
                }]
        })
        UtilizacaoMemoria_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        UtilizacaoMemoria_Grid.superclass.initEvents.call(this);
    }

    , _onRefresh: function(){
        //Ext.Msg.alert('<font color=red>Debug Message!</font>', this.uptime);
        this.store.load();
    }
});
Ext.reg('e-UtilizacaoMemoria_Grid', UtilizacaoMemoria_Grid);

//=======================================================================================================
/**
 *
 */
var UtilizacaoDiscoLocal_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'UtilizacaoDiscoLocal_Grid'
    , title : 'Sistemas de Arquivos'
    , width : 600
    , height: 200
    , iconCls: 'silk-chart_pie'
    , loadMask: true

    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url            : 'main.php'
            , root           : 'rows'
            , idProperty     : 'id'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'ServerStatus'
                , action : 'getFileSystem'
            }
            , fields:[
                {name:'id'         , type:'int'}
                , {name:'particao' , type:'string'}
                , {name:'montagem' , type:'string'}
                , {name:'tipo'     , type:'string'}
                , {name:'percent'  , type:'string'}
                , {name:'utilizada', type:'string'}
                , {name:'livre'    , type:'string'}
                , {name:'total'    , type:'string'}
            ]
        });

        Ext.apply(this,{

            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , forceFit:true
                , deferEmptyText : false
            }
            , defaults: {
                sortable      : false
                , menuDisabled: true
                , hideable    : false
                , groupable   : false
            }
            , columns:[{
                    dataIndex: 'particao'
                    , header : 'Parti&ccedil;&atilde;o'
                    , width  : 100
                },{
                    dataIndex: 'montagem'
                    , header : 'Montagem'
                    , width  : 100
                },{
                    dataIndex: 'tipo'
                    , header : 'Tipo'
                    , width  : 100
                },new Ext.ux.ProgressColumn({
                    header: '% Utilizado'
                    , width: 200
                    , dataIndex: 'percent'
                    , align: 'center'
                    , renderer: function(value, meta, record, rowIndex, colIndex, store, pct) {
                        return Ext.util.Format.number(pct, "0.00%");
                    }
                }),{
                    dataIndex: 'livre'
                    , header : 'Livre'
                    , width  : 100
                },{
                    dataIndex: 'utilizada'
                    , header : 'Utilizado'
                    , width  : 100
                },{
                    dataIndex: 'total'
                    , header : 'Total'
                    , width  : 100
                }]
        })
        UtilizacaoDiscoLocal_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        UtilizacaoDiscoLocal_Grid.superclass.initEvents.call(this);
    }

    , _onRefresh: function(){
        //Ext.Msg.alert('<font color=red>Debug Message!</font>', this.uptime);
        this.store.load();
    }
});
Ext.reg('e-UtilizacaoDiscoLocal_Grid', UtilizacaoDiscoLocal_Grid);

//=======================================================================================================



var ServerStatus_Panel = Ext.extend(Ext.Panel,{

    // Atributos de controle de Permissoes
    id: 'ServerStatus_Panel_table'
    , perm_insert: 1
    , perm_delete: 1
    , perm_update: 1
    , perm_execut: 1

    , runner: new Ext.util.TaskRunner()

    , viewConfig: {
        forceFit:true
    }
    , baseCls:'x-plain'
    , autoScroll: true
    , layout :'table'
    , layoutConfig: {columns:2}

    // applied to child components
    , defaults: {
        frame:true
        , style: {
            marginLeft    : '10px'
            , marginTop   : '10px'
            , marginBottom: '10px'
        }
    }

    , initComponent: function() {

        Ext.apply(this,{
            items: [/*{
                xtype: 'panel'
                , title: 'panel1'
                , width : 200
                , height: 680
                , rowspan: 3
            },*/{
                xtype: 'e-InformacoesServidor_Grid'
                , id    : 'panel_uptime'
                , width : 250
                , height: 200
            },{
                xtype: 'e-UtilizacaoRede_Grid'
                , id    : 'Utilizacao_Rede'
                , width : 350
                , height: 200
            }/*,{
                xtype: 'panel'
                , title: 'panel4'
                , width : 150
                , height: 680
                , rowspan: 3
            }*/,{
                xtype: 'e-UtilizacaoMemoria_Grid'
                , id    : 'Utilizacao_Memoria'
                , colspan: 2
            },{
                xtype  : 'e-UtilizacaoDiscoLocal_Grid'
                , id   : 'Utilizacao_Disco'
                , colspan: 2
            }]
            // TopBar
            , tbar:[
            // Botão Refresh
                this.btnRefresh = new Ext.Button({
                    text     : 'Monitorar'
                    , id     : 'btnRefresh'
                    , iconCls: 'silk-control_repeat_blue'
                    , scope  : this
                    , enableToggle: true
                    , toggleHandler: function(button, state){
                        if (state) {
                            this._onBtnRefreshClick();
                        }
                        else {
                            this._onBtnStopClick();
                        }
                    }
                    , tooltip: {title:'Monitorar Status do Servidor',text:'Click para ativar atualiza&ccedil;&atilde;o autom&aacute;tica das informações, click novamenta para desativar ou click em parar.'}
                })
                , this.btnStop = new Ext.Button({
                    text     : 'Parar'
                    , id     : 'btnStop'
                    , iconCls: 'silk-control_pause_blue'
                    , scope  : this
                    , handler: this._onBtnStopClick
                    , disabled: true
                    , tooltip: {title:'Monitorar Status do Servidor',text:'Click para desativar atualiza&ccedil;&atilde;o autom&aacute;tica.'}
                })
            ]
        })

        ServerStatus_Panel.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        ServerStatus_Panel.superclass.initEvents.call(this);
        // depois de renderizado carrega as permissoes
        this.on('afterrender', this._onAfterRender, this);
    }

    // Metodos
    , _onBtnRefreshClick: function(){
        var task = {
            run: function(){
                Ext.getCmp('panel_uptime')._onRefresh();
                Ext.getCmp('Utilizacao_Rede')._onRefresh();
                Ext.getCmp('Utilizacao_Memoria')._onRefresh();
                Ext.getCmp('Utilizacao_Disco')._onRefresh();
            }
            //, interval: 30000 //30 second de espera
            , interval: 5000 //30 second de espera
        }
        this.runner.start(task);
        Ext.getCmp('main_statusbar').msg('job');
        Ext.getCmp('btnStop').enable();
    }

    , _onBtnStopClick: function(){
        Ext.getCmp('btnRefresh').toggle(false);
        Ext.getCmp('btnStop').disable();
        this.runner.stopAll();
        Ext.getCmp('main_statusbar').msg('ok');
    }

    // Destroy
    , onDestroy: function() {
        ServerStatus_Panel.superclass.onDestroy.apply(this,arguments);

       this.runner.stopAll();
    }

    // Controle de Permissoes
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
                , tela : 'status_servidor'
            }
            , success: function ( result, request ) {
                var data = Ext.decode(result.responseText);
                perm_insert = data.ins;
                perm_delete = data.del;
                perm_execut = data.exc;
                perm_update = data.upd;

                // Habilitando/Desabilitando os BotÃµes
                if (perm_insert == 0) {
                    //Ext.getCmp('btnNovo').disable();
                }
                if (perm_delete == 0) {
                    //Ext.getCmp('btnExcluir').disable();
                    //this.perm_delete = perm_delete;
                }
                if (perm_execut == 0) {

                }
                if (perm_update == 0) {
                    //this.setPermissaoUpd(perm_update);
                }
            }
        });
    }

});

Ext.reg('e-ServerStatus_Panel', ServerStatus_Panel);
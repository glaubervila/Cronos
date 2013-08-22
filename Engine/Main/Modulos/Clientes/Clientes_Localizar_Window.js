var Clientes_Localizar_Window = new Ext.extend(Ext.Window,{

    id: 'Clientes_Localizar_Window'
    , constrain  : true
    , width      : 500
    , height     : 300
    , title      : 'Localizar Cliente'
    , iconCls    : 'silk-find'
    , layout     : 'fit'
    , autoScroll : false

    //, closeAction  : 'hide'
    , main_url     : 'main.php'
    , main_class   : ''
    , metodo_submit: ''


    , constructor: function() {

        this.addEvents({
            seleciona_cliente: true
        });

        Clientes_Localizar_Window.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {

        this.grid_pesquisa = new Clientes_Localizar_Grid({
            id:'novo_orcamento_localizar_cliente'
            , listeners  :{
                scope  : this
                , seleciona_cliente : this.onSelecionaProduto
            }
        })

        Ext.apply(this,{

            items:[
                this.grid_pesquisa
            ]

        })
        Clientes_Localizar_Window.superclass.initComponent.call(this);
    }


    , onSelecionaProduto: function(grid,registroID){

        this.fireEvent('seleciona_cliente', this, registroID);

    }

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Clientes_Localizar_Grid.superclass.onDestroy.apply(this,arguments);
        this.grid_pesquisa = null;
    }

});

Ext.reg('e-Clientes_Localizar_Window', Clientes_Localizar_Window);


/**
 *
 */
var Clientes_Localizar_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'Clientes_Localizar_Grid'
/*    , title : 'Localizar Produtos'*/
    , loadMask: true

    , main_url   : 'main.php'
    , main_class : 'Clientes'
    , pk_id      : 'pk_id_cliente'
    , metodo_load: 'localizar_clientes'

    , constructor: function() {

        this.addEvents({
            seleciona_cliente: true
        });

        Clientes_Localizar_Grid.superclass.constructor.apply(this,arguments);
    }


    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url              : this.main_url
            , root           : 'rows'
            , idProperty     : 'pk_id_produto'
            //, autoLoad       : true
            , autoDestroy    : true
            , totalProperty  : 'totalCount'
            , baseParams     : {
                classe    : this.main_class
                , action  : this.metodo_load
                , limit   : 30
            }
            , fields:[
                {name:'pk_id_cliente'      , type:'int'}
                , {name:'nome'  , type:'string'}
            ]
        });

        this.sm = new Ext.grid.CheckboxSelectionModel({singleSelect:true});

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
            ,tbar: [/*{
                text     : 'Selecionar'
                , id     : 'btn_selecionar_cliente'
                , iconCls: 'silk-add'
                , scope  : this
                , handler: this._onBtnSelecionarClick
                , tooltip: {title:'Selecionar Registro',text:'Click para Selecionar um Registro'}
            }*/]
            , bbar: new Ext.PagingToolbar({
                store      : this.store
                , pageSize  : 30
                , displayInfo: true
                , displayMsg: 'Mostrando resultados {0} - {1} de {2}'
                , plugins: new Ext.ux.ProgressBarPager()

            })
            , columns:[
            this.sm
            ,{
                header      : 'C&oacute;digo'
                , dataIndex : 'pk_id_cliente'
                , width     : 30
            },{
                header      : 'Nome'
                , dataIndex : 'nome'
                //, width     : 150
            },{
                header      : 'CPF'
                , dataIndex : 'cpf'
                , hidden    : true
            },{
                header      : 'CNPJ'
                , dataIndex : 'cnpj'
                , hidden    : true
            },{
                header      : 'CNPJ'
                , dataIndex : 'cnpj'
                , hidden    : true
            },{
                header      : 'RG'
                , dataIndex : 'rg'
                , hidden    : true
            },{
                header      : 'Inscricao Estadual'
                , dataIndex : 'inscricao_estadual'
                , hidden    : true
            }]
            , sm: this.sm
            , plugins:new Ext.ux.grid.Search({
                readonlyIndexes:['pk_id_cliente']
                , position: 'top'
                , mode    : 'remote'
                , width   : 200
                , minChars: 3
            })
        })
        Clientes_Localizar_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Clientes_Localizar_Grid.superclass.initEvents.call(this);

        // DoubleClick na linha Dispara o Evento
        this.on({
            scope      : this
            , rowdblclick: this._onGridRowDblClick
        });

    }

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Clientes_Localizar_Grid.superclass.onDestroy.apply(this,arguments);

    }

    , _onGridRowDblClick: function( grid, rowIndex, e ){
        //busca registro da linha selecionada
        var record = grid.getStore().getAt(rowIndex);

        //extrai id
        var registroID = record.get(this.pk_id);


        this.fireEvent('seleciona_cliente', this, registroID);

        this.store.removeAll();
    }





});
Ext.reg('e-Clientes_Localizar_Grid', Clientes_Localizar_Grid);

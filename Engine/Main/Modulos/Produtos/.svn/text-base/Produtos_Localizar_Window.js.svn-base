var Produtos_Localizar_Window = new Ext.extend(Ext.Window,{

    id: 'Produtos_Localizar_Window'
    , constrain  : true
    , width      : 500
    , height     : 300
    , title      : 'Localizar Produto'
    , iconCls    : 'silk-find'
    , layout     : 'fit'
    , autoScroll : false

    , closeAction  : 'hide'
    //, main_url     : 'Main/PHP/ImageUpload.class.php'
    , main_url     : 'main.php'
    , main_class   : ''
    , metodo_submit: ''


    , constructor: function() {

        this.addEvents({
            seleciona_produto: true
        });

        Produtos_Localizar_Window.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {

        this.grid_pesquisa = new Produtos_Localizar_Grid({
            id:'grid_produtos_localizar_grid'
            , listeners  :{
                scope  : this
                , seleciona_produto : this.onSelecionaProduto
            }
        })

        Ext.apply(this,{

            items:[
                this.grid_pesquisa
            ]

        })
        Produtos_Localizar_Window.superclass.initComponent.call(this);
    }


    , onSelecionaProduto: function(grid,registroID){

        this.fireEvent('seleciona_produto', this, registroID);

    }

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Produtos_Localizar_Grid.superclass.onDestroy.apply(this,arguments);
        this.grid_pesquisa = null;
    }

});

Ext.reg('e-Produtos_Localizar_Window', Produtos_Localizar_Window);


/**
 *
 */
var Produtos_Localizar_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'Produtos_Localizar_Grid'
/*    , title : 'Localizar Produtos'*/
    , loadMask: true

    , main_url   : 'main.php'
    , main_class : 'Produtos'
    , pk_id      : 'pk_id_produto'
    , metodo_load: 'load_produtos'

    , constructor: function() {

        this.addEvents({
            seleciona_produto: true
        });

        Produtos_Localizar_Grid.superclass.constructor.apply(this,arguments);
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
                {name:'pk_id_produto'      , type:'int'}
                , {name:'descricao_curta'  , type:'string'}
                , {name:'descricao_longa'  , type:'string'}
                , {name:'preco'  , type:'float'}
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
                , id     : 'btn_selecionar_produto'
                , iconCls: 'silk-add'
                , scope  : this
                , handler: this._onBtnSelecionarProdutoClick
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
                dataIndex   : 'pk_catalogo_produto'
                , hidden    : true
            },{
                header      : 'C&oacute;digo'
                , dataIndex : 'pk_id_produto'
                , width     : 30
            },{
                header      : 'Descrição Curta'
                , dataIndex : 'descricao_curta'
                //, width     : 150
                , hidden    : true
            },{
                header      : 'Descrição Longa'
                , dataIndex : 'descricao_longa'
                //, width     : 150
            },{
                header      : 'Preço'
                , dataIndex : 'preco'
                , width     : 50
            }]
            , sm: this.sm
            , plugins:new Ext.ux.grid.Search({
                readonlyIndexes:['pk_id_produto', 'preco']
                , position: 'top'
                , mode    : 'remote'
                , width   : 200
                , minChars: 3
            })
        })
        Produtos_Localizar_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Produtos_Localizar_Grid.superclass.initEvents.call(this);

        // DoubleClick na linha Dispara o Evento
        this.on({
            scope      : this
            , rowdblclick: this._onGridRowDblClick
        });

    }

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Produtos_Localizar_Grid.superclass.onDestroy.apply(this,arguments);

    }

    , _onGridRowDblClick: function( grid, rowIndex, e ){
        //busca registro da linha selecionada
        var record = grid.getStore().getAt(rowIndex);

        //extrai id
        var registroID = record.get(this.pk_id);


        this.fireEvent('seleciona_produto', this, registroID);

        this.store.removeAll();
    }





});
Ext.reg('e-Produtos_Localizar_Grid', Produtos_Localizar_Grid);

/**
 *
 */
var Catalogo_Produtos_Grid = Ext.extend(Ext.grid.GridPanel,{

    id:'Catalogo_Produtos_Grid'
    , identificacao : '5006' // Identificacao para permissoes
    , title : 'Produtos no Catalogo'
    //, layout:'fit'
    //, iconCls:''
    , loadMask: true

    , main_url   : 'main.php'
    , main_class : 'Catalogos'
    , pk_id      : 'pk_catalogo'
    , metodo_load: 'getProdutosCompletoInCatalogoById'
    , metodo_delete: 'delete_catalogo_produtos'


    , pk_catalogo: 0

    // Esta Grid Necessita da chave de um Catalogo
    , setCatalogoID: function(pk_catalogo) {
        this.pk_catalogo = pk_catalogo;
    }

    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url              : this.main_url
            , root           : 'rows'
            , idProperty     : 'pk_catalogo_produto'
            //, autoLoad       : true
            , autoDestroy    : true
            , totalProperty  : 'total_count'
            , baseParams     : {
                classe    : this.main_class
                , action  : this.metodo_load
                , pk_catalogo : this.pk_catalogo
//                , limit  : 1000
            }
            , fields:[
                {name:'pk_catalogo_produto', type:'int'}
                , {name:'fk_catalogo'      , type:'string'}
                , {name:'pk_id_produto'    , type:'string'}
                , {name:'descricao_longa'  , type:'string'}
                , {name:'quantidade'       , type:'string'}
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
/*            , bbar: new Ext.PagingToolbar({
                store      : this.store
                , pageSize  : 1000
                , displayInfo: true
                , displayMsg: 'Mostrando resultados {0} - {1} de {2}'
                , plugins: new Ext.ux.ProgressBarPager()

            })*/
            , columns:[{
                dataIndex   : 'pk_catalogo_produto'
                , hidden    : true
            },{
                header      : 'C&oacute;digo'
                , dataIndex : 'pk_id_produto'
                , width     : 30
            },{
                header      : 'Descrição Longa'
                , dataIndex : 'descricao_longa'
                , width     : 150
            },{
                header      : 'Quantidade'
                , dataIndex : 'quantidade'
            }]
        })
        Catalogo_Produtos_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Catalogo_Produtos_Grid.superclass.initEvents.call(this);
    }

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Catalogo_Produtos_Grid.superclass.onDestroy.apply(this,arguments);

    }

});
Ext.reg('e-Catalogo_Produtos_Grid', Catalogo_Produtos_Grid);

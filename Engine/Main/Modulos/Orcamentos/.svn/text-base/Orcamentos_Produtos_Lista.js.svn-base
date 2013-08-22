var Orcamentos_Produtos_Lista = Ext.extend(Ext.grid.GridPanel,{

    border     : false
    , stripeRows : true
    , loadMask   : true
    , id         : 'Orcamentos_Produtos_Lista'

    , main_url   : 'main.php'
    , main_class : 'Orcamentos_Servidor'
    , pk_id      : 'pk_orcamento_produto'
    , metodo_load  : 'getOrcamentoProdutos'
    , metodo_delete: 'delete_orcamento_produtos'
    , metodo_submit: ''

    , IdRegistro   : 0
    , PkOrcamento  : 0

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }
    , setPkOrcamento: function(PkOrcamento) {
        this.PkOrcamento = PkOrcamento;
    }
    , disableSelection:false


    , constructor: function() {
        Orcamentos_Produtos_Lista.superclass.constructor.apply(this,arguments);

        this.addEvents({

        });
    }


    ,initComponent: function() {

        //store do grid
        this.store = new Ext.data.JsonStore({
            url              : this.main_url
            , root           : 'rows'
            , idProperty     : this.pk_id
            , totalProperty  : 'totalCount'
            , autoSave       : false
            , baseParams     : {
                classe     : this.main_class
                , action   : this.metodo_load
                , pk_orcamento: this.IdRegistro
            }
            , fields:[
                {name:this.pk_id         , type:'string'}
                , {name: 'fk_orcamento'  , type:'string'}
                , {name: 'fk_id_produto' , type:'int'}
                , {name: 'descricao_curta', type:'string'}
                , {name: 'quantidade'    , type:'float'}
                , {name: 'preco'         , type:'float'}
                , {name: 'valor_total'   , type:'float'}
                , {name: 'observacao_produto', type: 'string'}
                , {name: 'action_observacao', type: 'string'}
                , {name: 'qtip_observacao', type: 'string'}
            ]
        });

        //demais atributos do grid
        Ext.apply(this,{
            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , forceFit:true
                , deferEmptyText : false
            }
            , columns:[
            new Ext.grid.RowNumberer()
            ,{
                dataIndex   : this.pk_id
                , hidden    : true
            },{
                dataIndex   : 'fk_id_produto'
                , header    : 'C&oacute;digo'
                , sortable  : true
            },{
                dataIndex   : 'descricao_curta'
                , header    : 'Descri&ccedil;&atilde;o'
                , sortable  : true
            },{
                dataIndex   : 'quantidade'
                , header    : 'Quantidade'
                , sortable  : true
            },{
                dataIndex   : 'preco'
                , header    : 'Valor Unit&aacute;rio'
                , renderer  : 'brMoney'
                , sortable  : true
            },{
                dataIndex   : 'valor_total'
                , header    : 'Valor Total'
                , renderer  : 'brMoney'
                , sortable  : true
            }]
        })

        Orcamentos_Produtos_Lista.superclass.initComponent.call(this);
    }
});
Ext.reg('e-Orcamentos_Produtos_Lista', Orcamentos_Produtos_Lista);
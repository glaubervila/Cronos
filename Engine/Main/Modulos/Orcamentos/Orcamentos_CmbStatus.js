// Combo Status Pedido
// Status:
//     0 - Aberto     [Azul]
//     1 - Fechado    [Verde]
//     2 - Cancelado  [Vermelho]
//     3 - À Separar  [Amarelo]
//     4 - Aguardando Pagamento [  ]
//     5 - Em Entrega [Marrom]
//     6 - Finalizado [Cinza]

var Cmb_Status_Pedido = Ext.extend(Ext.form.ComboBox,{
    root             : 'rows'
    , fieldLabel     : 'Status'
    , hiddenName     : 'status'
    , triggerAction  : 'all'
    , forceSelection : true
    , width          : 190
    , mode           : 'local'
    , valueField     : 'id'
    , displayField   : 'status'
    , emptyText      : 'Selecione'
    , store: new Ext.data.JsonStore({
        url            : 'main.php'
        , root           : 'rows'
        , idProperty     : 'id'
        , totalProperty  : 'totalCount'
        , autoLoad       : true
        , autoDestroy    : true
        //, mode           : 'local'
        , baseParams     : {
            classe  : 'Orcamentos_Servidor'
            , action : 'getStoreStatus'
        }
        , fields:[
            {name:'id'    ,  type:'string'}
            , {name:'status',  type:'string'}
            , {name:'cor'   ,  type:'string'}
        ]
    })
});
Ext.reg('e-Cmb_Status_Pedido', Cmb_Status_Pedido);

/*    , store: new Ext.data.SimpleStore({
        fields:['id', 'Valor', 'Label']
        , data: [
//             [0, '0', 'Aberto']
//             , [1, '1', 'Fechado']
            [2, '2', 'Cancelado']
            , [3, '3', 'À Separar']
            , [4, '4', 'Aguardando Pagamento']
            , [5, '5', 'Em Entrega']
            , [6, '6', 'Finalizado']
        ]
    })*/
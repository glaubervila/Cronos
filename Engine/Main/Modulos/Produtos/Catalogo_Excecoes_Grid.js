/**
 *
 */
var Catalogo_Excecoes_Grid = Ext.extend(Ext.grid.GridPanel,{

    id    :'Catalogo_Excecoes_Grid'

    , identificacao : '5006' // Identificacao para permissoes
    , title : 'Produtos no Catalogo'
    //, layout:'fit'
    //, iconCls:''
    , loadMask: true

    , main_url   : 'main.php'
    , main_class : 'Catalogos'
    , pk_id      : 'pk_catalogo_produto'
    , metodo_load: 'getExcecoesCompletoInCatalogoById'
    , metodo_delete: 'delete_CatalogoProduto'
    , metodo_insert: 'insert_InCatalogoProdutoExcessao'


    , pk_catalogo: 0

    // Esta Grid Necessita da chave de um Catalogo
    , setCatalogoID: function(pk_catalogo) {
        this.pk_catalogo = pk_catalogo;
    }

    , constructor: function() {

        // adiciono um evento a classe. Esse evento sera disparado posteriormente quando o clica no botao salvar ou excluir
        this.addEvents({
            salvar: true
            , excluir: true
        });

        Catalogos_Form.superclass.constructor.apply(this,arguments);
    }


    , initComponent: function(){

        this.store = new Ext.data.JsonStore({
            url              : this.main_url
            , root           : 'rows'
            , idProperty     : this.pk_id
            //, autoLoad       : true
            , autoDestroy    : true
            , totalProperty  : 'total_count'
            , baseParams     : {
                classe    : this.main_class
                , action  : this.metodo_load
                , pk_catalogo : this.pk_catalogo
                , limit  : 30
            }
            , fields:[
                {name:'pk_catalogo_produto', type:'int'}
                , {name:'fk_catalogo'      , type:'string'}
                , {name:'pk_id_produto'    , type:'string'}
                , {name:'descricao_longa'  , type:'string'}
                , {name:'quantidade'       , type:'string'}
            ]
        });


        this.sm = new Ext.grid.CheckboxSelectionModel();

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
            ,tbar: [{
                text     : 'Novo'
                , id     : 'btn_nova_catalogo_excessao'
                , iconCls: 'silk-add'
                , scope  : this
                , handler: this.onBtnNovoClick
                , tooltip: {title:'Criar Novo Registro',text:'Click para adicionar um novo registro'}
            },{
                text     : 'Excluir'
                , id     : 'btn_excluir_catalogo_excessao'
                , iconCls: 'silk-delete'
                , tooltip: {title:'Excluir Selecionados',text:'Selecione uma ou mais linhas para excluir os registros. <b>Dica:</b> Para selecionar mais de uma linha segure a tecla "Crtl"'}
                , scope  : this
                , handler: this._onBtnExcluirSelecionadosClick
            }]

            , columns:[
            new Ext.grid.RowNumberer()
            , this.sm
            ,{
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
            , sm: this.sm
        })
        Catalogo_Excecoes_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Catalogo_Excecoes_Grid.superclass.initEvents.call(this);
    }

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Catalogo_Excecoes_Grid.superclass.onDestroy.apply(this,arguments);

    }

    , onBtnNovoClick:function(){

        this.criaWindowPesquisa();
        this.window_pesquisa.show();
    }

    , criaWindowPesquisa: function(){
        if(!this.window_pesquisa) {
            this.window_pesquisa = new Produtos_Localizar_Window({
                listeners  :{
                    scope  : this
                    , seleciona_produto : this.onSelecionaProduto
                }
            })
        }
        return this.window_pesquisa
    }

    , onSelecionaProduto:function(obj, registroId){
        //alert(registroId);
        this.window_pesquisa.hide();
        this.insere_produto_excessao(registroId, this.pk_catalogo);
    }

    , insere_produto_excessao:function (registroId){

        //alert('Registro: ' + registroId + 'Catalogo: '+pk_catalogo);

        Ext.Ajax.request({
            url    : this.main_url
            , params : {
                classe    : this.main_class
                , action  : this.metodo_insert
                , pk_catalogo  : this.pk_catalogo
                , pk_id_produto: registroId
            }
            , scope  : this
            , success: function(response){
                response = Ext.decode(response.responseText);
                if( response.success ){
                    Ext.getCmp('main_statusbar').msg('ok');
                    //this.store.reload();
                    this.fireEvent('salvar',this);
                }
                else {
                    Ext.getCmp('main_statusbar').msg('error');
                    Ext.MessageBox.show({ title:'Desculpe!', msg: response.msg, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
                }
            }
        });
    }

    , _onBtnExcluirSelecionadosClick: function(){
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
                            , 'pk_catalogo_produto[]': ids
                            , 'pk_catalogo': this.pk_catalogo
                        }
                        ,scope  : this
                        ,success: function() {
                            this.el.unmask();
                            this.store.removeAll();
                            //this.store.reload();
                            Ext.getCmp('main_statusbar').msg('ok');
                            this.fireEvent('excluir',this);
                        }
                        , failure: function(form , action){
                            var obj = Ext.decode(action.response.responseText);
                            this.el.unmask();
                            this.hide();
                            Ext.getCmp('main_statusbar').msg('error');
                            Ext.MessageBox.show({ title:'Desculpe!', msg: obj.msg, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
                        }
                    });
                }
            },this);
        }
    }

});
Ext.reg('e-Catalogo_Excecoes_Grid', Catalogo_Excecoes_Grid);
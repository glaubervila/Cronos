/**
 * @package     : Produtos
 * @name        : Catalogos_Form
 * @version     : 1.0
 * @Diretorio   : Main/Modulos/Produtos/
 * @date        : 23/11/2010
 */
var Catalogos_Form = Ext.extend(Ext.Window,{

    id: 'Catalogos_Form'
    , identificacao : '5005' // Identificacao para permissoes

    , main_url   : 'main.php'
    , main_class : 'Catalogos'
    , pk_id      : 'pk_catalogo'
    , metodo_load  : 'getCatalogoById'
    , metodo_delete: 'delete_catalogos'
    , metodo_submit: 'CriaAtualiza'

    , IdRegistro: 0

    , modal    : true
    , constrain: true
    , maximizable: false

    , width  : 900
    , height : 500
    , title  : 'Cadastro de Catalogo de Produtos'
    , layout : 'border'


    // Essa janela sera reaproveitada, por isso closeAction deve ser HIDE
    , closeAction: 'hide'

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }
    , getRegistroID: function(){
        return this.IdRegistro;
    }

    // Permissoes 0 Desabilitado / 1 Habilitado
    , perm_insert: 0
    , perm_delete: 0
    , perm_update: 0
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


        // adiciono um evento a classe. Esse evento sera disparado posteriormente quando o clica no botao salvar
        this.addEvents({
            salvar: true
            , excluir: true
        });

        Catalogos_Form.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';

        //formulario
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:5px;'
            , border         : true
            , autoScroll     : false
            , labelAlign     : 'top'
            , region         : 'west'
            , width : 200
            , defaults:{
                width : 170
            }
            , items:[{
                fieldLabel   : 'C&oacute;digo'
                , name       : 'pk_catalogo'
                , xtype      : 'textfield'
                , readOnly   :true
                //, allowBlank : false
            },{
                fieldLabel   : 'Quantidade Minima em Estoque'
                , name       : 'quantidade_minima'
                , xtype      : 'textfield'
                , allowBlank : false
            },{
                fieldLabel   : 'Coment&aacute;rio'
                , name       : 'comentario'
                , xtype      : 'textarea'
            },{
                fieldLabel   : 'Total de Produtos'
                , name       : 'quantidade_total_produtos'
                , xtype      : 'textfield'
                , readOnly   :true
            },{
                fieldLabel   : 'Data Inclus&atilde;o'
                , name       : 'dt_inclusao'
                , xtype      : 'textfield'
                , readOnly   :true
            },{
                fieldLabel   : 'Data Aleta&ccedil;&atilde;o'
                , name       : 'dt_alteracao'
                , xtype      : 'textfield'
                , readOnly   :true
            }]
        })

        this.grid_catalogo_produtos = new Catalogo_Produtos_Grid({
            id:'Catalogo_Produtos_Grid_on_Form'
            , title : ''
            //, iconCls:''
            , loadMask: true
        })
        this.grid_catalogo_excecoes = new Catalogo_Excecoes_Grid({
            id:'Catalogo_Excecoes_Grid_on_Form'
            , title : ''
            //, iconCls:''
            , loadMask: true
            , listeners  :{
                scope  : this
                , salvar : this.show
                , excluir: this.show
            }

        })


        this.tabPanelCatalogoProdutos = new Ext.TabPanel({
            region      : 'center'
            , id        : 'tabPanelCatalogoProdutos'
            , activeTab : 0
            , defaults  : {closable: false}
            , items     : [{
                title   : 'Produtos Exceções'
                , layout: 'fit'
                , items   : [
                    this.grid_catalogo_excecoes
                ]
            },{
                title   : 'Produtos Catalogo'
                , layout: 'fit'
                , items   : [
                    this.grid_catalogo_produtos
                ]
            }]
        });

        Ext.apply(this,{
            items  : [
                this.formPanel
                , this.tabPanelCatalogoProdutos
            ]
            , bbar : ['->'
            , this.btnSalvar = new Ext.Button({
                text     : 'Salvar'
                , iconCls: 'silk-disk'
                , scope  : this
                , handler: this._onBtnSalvarClick
            })
            , this.btnExcluir = new Ext.Button({
                text     : 'Excluir'
                , iconCls: 'silk-delete'
                , scope  : this
                , handler: this._onBtnDeleteClick
            })
            ,{
                text     : 'Cancelar'
                , iconCls: 'silk-cross'
                , scope  : this
                , handler: this._onBtnCancelarClick
            }]
        })
        Catalogos_Form.superclass.initComponent.call(this);
    }

    /*
     * Sobreescrevo o metodo show de Ext.Window para aplicar a seguinte rotina:
     * Se IdRegistro foi informado, carrega o form, senao, reseta o form.
     */
    , show: function() {
        Catalogos_Form.superclass.show.apply(this,arguments);
        this.formPanel.getForm().reset();

        //se tem usuario
        if(this.IdRegistro !== 0) {
            this.btnExcluir.show();
            // Tratamento de Permissoes
            // o usuario pode excluir?
            if(this.perm_delete == 0){
                this.btnExcluir.disable();
            }
            // o usuario pode alterar?
            if(this.perm_update == 0){
                this.btnSalvar.disable();;
            }

            this.el.mask('Carregando informa&ccedil;&otilde;es');
            Ext.getCmp('main_statusbar').msg('load');

            /*
             * Carregando o formulario. Ele deve respeitar algums formatos especificiados na documentacao ext de
             * Ext.form.Action.Load, como por exemplo conter uma propriedade success e data.
             */
            this.formPanel.getForm().load({
                url : this.main_url
                , params : {
                    classe   : this.main_class
                    , action : this.metodo_load
                    , 'pk_catalogo': this.IdRegistro
                }
                , scope: this
                , success: this._onFormLoad
            });
            // habilitando o painel de abas
            this.tabPanelCatalogoProdutos.enable();
        }
        //se nao existir usuario
        else {
            //nao pode excluir
            this.btnExcluir.hide();
            if(this.perm_insert == 0){
                this.btnSalvar.disable();;
            }

            // Resetando o formulario
            this.formPanel.getForm().reset();
            this.grid_catalogo_produtos.store.removeAll();
            this.grid_catalogo_excecoes.store.removeAll();

            // Se for um novo desabilita as abas
            this.tabPanelCatalogoProdutos.disable();
        }
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Catalogos_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
        this.grid_catalogo_produtos = null;
        this.grid_catalogo_excecoes = null;
    }

    // Listeners

    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;
        // tiro a mascara
        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();

        // Depois de Carregar o Form deve setar o pk_catalogo na grid_catalogo_produtos
        // e Mandar ela Recarregar
        this.grid_catalogo_produtos.setCatalogoID(data.pk_catalogo);
        this.grid_catalogo_produtos.store.setBaseParam('pk_catalogo', data.pk_catalogo);
        this.grid_catalogo_produtos.store.load();

        this.grid_catalogo_excecoes.setCatalogoID(data.pk_catalogo);
        this.grid_catalogo_excecoes.store.setBaseParam('pk_catalogo', data.pk_catalogo);
        this.grid_catalogo_excecoes.store.load();

    }

    // Listener disparado ao clicar no botao salvar
    , _onBtnSalvarClick: function() {
        //pego o formulario
        var form = this.formPanel.getForm();
        //verifico se e valido
        if(!form.isValid()) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            return false;
        }
        // crio uma mascara
        this.el.mask('Salvando informa&ccedil;&otilde;es');
        Ext.getCmp('main_statusbar').msg('saving');

        // Submitando formulario
        form.submit({
            url: this.main_url
            , params: {
                classe       : this.main_class
                , action     : this.metodo_submit
                , 'pk_catalogo' : this.IdRegistro
            }
            , scope:this
            //ao terminar de submitar
            , success: function() {
                //tiro mascara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('save');
                //esconde janela
                this.hide();
                // Muito importante! Aqui o evento salvar sera disparado. Todos os listeners que foram associados a esse evento serao notificados, como por exemplo, o listener _onCadastroGruposUsuarios Salvar da classe Grid.
                this.fireEvent('salvar',this);
            }
        });
    }

    // Listener disparado ao clicar em excluir
    , _onBtnDeleteClick: function() {
        Ext.Msg.confirm('Confirma&ccedil;&otilde;o','Deseja mesmo excluir esse registro?',function(opt) {
            if(opt === 'no') {
                return
            }
            else {
                this.el.mask('Excluindo Registro.');
                Ext.getCmp('main_statusbar').msg('job');
                Ext.Ajax.request({
                    url: this.main_url
                    , params: {
                        classe   : this.main_class
                        , action : this.metodo_delete
                        , 'pk_catalogo' : this.IdRegistro
                    }
                    , scope: this
                    , success: function() {
                        this.el.unmask();
                        Ext.getCmp('main_statusbar').msg('ok');
                        this.hide();
                        // Evento personalizado excluir sendo disparado
                        this.fireEvent('excluir',this);
                    }
                })
            }
        },this)
    }

    // Listener disparado ao clicar em cancelar
    , _onBtnCancelarClick: function() {
        Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo cancelar esse cadastro?',function(opt) {
            if(opt === 'yes') {
                //esconde window
                this.hide();
            }
        },this)
    }
});
Ext.reg('e-Catalogos_Form', Catalogos_Form);



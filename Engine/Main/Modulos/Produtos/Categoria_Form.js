/**
 * @package     : Produtos
 * @name        : Categoria_Form
 * @version     : 1.0
 * @Diretorio   : Main/Modulos/Produtos/
 * @date        : 23/11/2010
 */
var Categoria_Form = Ext.extend(Ext.Window,{

    id: 'Categoria_Form'
    , identificacao : '5003' // Identificacao para permissoes

    , main_url   : 'main.php'
    , main_class : 'Categoria'
    , pk_id      : 'pk_id_categoria'
    , metodo_load  : 'getCategoria'
    , metodo_delete: 'deleteCategoria'
    , metodo_submit: 'criaAtualiza'

    , IdRegistro: 0

    , modal  : true
    , constrain: true
    //, maximizable: true
    , width  : 400
    , height : 250
    , title  : 'Cadastro de Fabricantes'
    , layout : 'fit'

    // Essa janela sera reaproveitada, por isso closeAction deve ser HIDE
    , closeAction: 'hide'

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
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

        Categoria_Form.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';

        this.color_palet = new Ext.ColorPalette({
            listeners  :{
                scope: this
                , 'select': function(palette, selColor) {
                    Ext.getCmp('txt_categorias_color_select_window').setValue(selColor);
                    Ext.getCmp('categorias_color_select_window').hide();
                }
            }
        });

        this.color_select_panel = new Ext.Window({
            id: 'categorias_color_select_window'
            , title: 'Selecione uma Cor'
            , closeAction: 'hide'
            , iconCls:'silk-color-wheel'
            , items:[this.color_palet]
        })

        this.btnColor = new Ext.Button({
                text     : ''
                , iconCls: 'silk-color-wheel'
                , scope  : this
                , handler: function (){
                    this.color_select_panel.show();
                }
                , col:true
                , width:30
            })
        //formulario
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:10px;'
            , border         : false
            , autoScroll     : true
            //, labelAlign     : 'top'
            , items:[{
                fieldLabel   : 'Categoria'
                , name       : 'categoria'
                , xtype      : 'textfield'
                , width      : 200
                , minLength  : 3
                , maxLength  : 100
                , allowBlank : false
            },{
                fieldLabel   : 'Código de Cor (ex:FF00CC)'
                , id         : 'txt_categorias_color_select_window'
                , name       : 'codigo_cor'
                , xtype      : 'textfield'
                , width      : 50
                //, minLength  : 6
                , maxLength  : 7
            }, this.btnColor]
        })

        Ext.apply(this,{
            items  : this.formPanel
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
        Categoria_Form.superclass.initComponent.call(this);
    }



    /*
     * Sobreescrevo o metodo show de Ext.Window para aplicar a seguinte rotina:
     * Se IdRegistro foi informado, carrega o form, senao, reseta o form.
     */
    , show: function() {
        Categoria_Form.superclass.show.apply(this,arguments);
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
                    , 'pk_id_categoria': this.IdRegistro
                }
                , scope: this
                , success: this._onFormLoad
            });
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
        }
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Categoria_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
    }

    // Listeners

    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;
        // tiro a mascara
        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();
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
                , 'pk_id_categoria' : this.IdRegistro
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
                        , 'pk_id_categoria' : this.IdRegistro
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
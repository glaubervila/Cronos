/**
 *
 * @Diretorio: Main/Modulos/Acesso/
 */
var Usuarios_Form = Ext.extend(Ext.Window,{

    IdUsuario: 0

    , modal  : true
    , constrain: true
    //, maximizable: true
    , width  : 450
    , height : 300
    , title  : 'Cadastro de Usu&aacute;rio'
    , layout : 'fit'

    // Essa janela será reaproveitada, por isso closeAction deve ser HIDE
    , closeAction: 'hide'

    // Permissões 0 Desabilitado / 1 Habilitado
    , perm_insert: 1
    , perm_delete: 1
    , perm_update: 1

    , setPermissaoIns: function(perm_insert) { this.perm_insert = perm_insert; }
    , setPermissaoDel: function(perm_delete) { this.perm_delete = perm_delete; }
    , setPermissaoUpd: function(perm_update) { this.perm_update = perm_update; }

    //id do usuário
    , setUsuarioID: function(IdUsuario) {
        this.IdUsuario = IdUsuario;
    }

    ,constructor: function() {
    /*
     * Aqui eu adiciono um evento personalizado à classe. Esse evento é disparado posteriormente quando o usuário
     * clica no botão salvar
     */
        this.addEvents({
            salvar: true
            , excluir: true
        });

        Usuarios_Form.superclass.constructor.apply(this, arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';

        // ComboBox Grupo de Usuarios
        this.cmbGrupos = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'combo_grupos_usuarios'
            , fieldLabel     : 'Grupo'
            , hiddenName     : 'Grupo'
            , triggerAction  : 'all'
            , valueField     : 'id'
            , displayField   : 'Grupo'
            , emptyText      : 'Selecione um Grupo'
            , width          : 155
            , allowBlank     : false
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'id'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe : 'GruposUsuarios'
                    , action  : 'getGrupos'
                }
                , fields:[
                    {name:'id'      , type:'int'}
                    , {name:'Grupo' , type:'string'}
                ]
            })
        })

        //formulário
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:10px;'
            , border         : false
            , autoScroll     : true
            , defaultType    : 'textfield'
            , items:[this.cmbGrupos
            , {
                fieldLabel   : 'Nome'
                , name       : 'Nome'
                , allowBlank : false
                , minLength  : 3
                , maxLength  : 50
                , allowBlank : false
            },{
                fieldLabel   : 'Login'
                , name       : 'Login'
                , minLength  : 3
                , maxLength  : 20
                , allowBlank : false
            },{
                inputType     : 'password'
                , fieldLabel  : 'Senha'
                , name        : 'Senha'
                , id          : 'pass'
                , minLength   : 4
                , maxLength   : 10
                , allowBlank  : false
            },{
                inputType      : 'password'
                , fieldLabel   : 'Repita Senha'
                , name         : 'senha-cfrm'
                , vtype        : 'password'
                , minLength    : 4
                , maxLength    : 10
                , initialPassField: 'pass' // id of the initial password field
                , allowBlank   : false
            }]
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
        // Cria a Regra de Validação do campo senha
        Ext.apply(Ext.form.VTypes, {
            password : function(val, field) {
                if (field.initialPassField) {
                    var pwd = Ext.getCmp(field.initialPassField);
                    return (val == pwd.getValue());
                }
                return true;
            },
            passwordText : 'Digite a mesma senha nos dois campos.'
        })

        Usuarios_Form.superclass.initComponent.call(this);
    }

    /*
     * Sobreescrevo o método show de Ext.Window para aplicar a seguinte rotina:
     * Se IdUsuario foi informado, carrega o form, senão, reseta o form.
     */
    , show: function() {
        Usuarios_Form.superclass.show.apply(this,arguments);
        this.formPanel.getForm().reset();
        //se tem usuario
        if(this.IdUsuario !== 0) {
            this.btnExcluir.show();
            // Tratamento de Permissões
            // o usuário pode excluir?
            if(this.perm_delete == 0){
                this.btnExcluir.disable();
            }
            // o usuário pode alterar?
            if(this.perm_update == 0){
                this.btnSalvar.disable();;
            }

            this.el.mask('Carregando informa&ccedil;&otilde;es');
            Ext.getCmp('main_statusbar').msg('load');

            /*
             * Carregando o formulário. Ele deve respeitar algums formatos especificiados na documentação ext de
             * Ext.form.Action.Load, como por exemplo conter uma propriedade success e data.
             */
            this.formPanel.getForm().load({
                url : 'main.php'
                , params : {
                    classe   : 'Usuarios'
                    , action: 'getUsuario'
                    , id    : this.IdUsuario
                }
                , scope: this
                , success: this._onFormLoad
            });
        }
        //se não existir usuario
        else {
            //não pode excluir
            this.btnExcluir.hide();
            if(this.perm_insert == 0){
                this.btnSalvar.disable();;
            }
            // Resetando o formulário
            this.formPanel.getForm().reset();
        }
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Usuarios_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
    }

    // Listeners

    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;
        // tiro uma máscara
        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();
    }

    // Listener disparado ao clicar no botão salvar
    , _onBtnSalvarClick: function() {
        //pego o formulário
        var form = this.formPanel.getForm();
        //verifico se é valido
        if(!form.isValid()) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            return false;
        }
        // crio uma máscara
        this.el.mask('Salvando informa&ccedil;&otilde;es');
        Ext.getCmp('main_statusbar').msg('saving');

        // Submitando formulário
        form.submit({
            url: 'main.php'
            , params: {
                classe   : 'Usuarios'
                , action: 'criaAtualizaUsuario'
                , id    : this.IdUsuario
            }
            , scope:this
            //ao terminar de submitar
            , success: function() {
                //tiro máscara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('save');
                //esconde janela
                this.hide();
                // Muito importante! Aqui o evento salvar é disparado. Todos os listeners que foram associados a esse evento serão notificados, como por exemplo, o listener _onCadastroUsuarioSalvar da classe Grid.
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
                    url: 'main.php'
                    , params: {
                        classe   : 'Usuarios'
                        , action: 'deleteUsuarios'
                        , id    : this.IdUsuario
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
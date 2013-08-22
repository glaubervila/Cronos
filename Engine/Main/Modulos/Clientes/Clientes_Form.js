/**
 * @date     : 16/06/2010
 * @Diretorio: Main/Modulos/Clientes/
 */
var Clientes_Form = Ext.extend(Ext.Window,{

    id: 'Clientes_Form'
    , identificacao : '4001' // Identificacao para permissoes

    , IdRegistro: 0

    , modal      : true
    , constrain  : true
    //, maximized  : true
    , width      : 900
    , height     : 500
    , title      : 'Cadastro de Clientes'
    , layout     : 'fit'
    , autoScroll : true


    // Essa janela sera reaproveitada, por isso closeAction deve ser HIDE
    , closeAction: 'hide'

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

        Clientes_Form.superclass.constructor.apply(this,arguments);
    }

    , setRegistroID: function(IdRegistro) {
        this.IdRegistro = IdRegistro;
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';

        // ComboBox Lojas
        this.cmbLojas = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'clientes_combo_Lojas'
            , fieldLabel     : 'Loja de Origem'
            , hiddenName     : 'fk_id_loja'
            , triggerAction  : 'all'
            , valueField     : 'id'
            , displayField   : 'nome'
            , emptyText      : 'Selecione uma Loja'
            , width          : 150
            , mode           : 'local'
            , allowBlank     : false
            , col            : true
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'id'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe : 'Lojas'
                    , action  : 'getLojasCmb'
                }
                , fields:[
                    {name:'id'     , type:'int'}
                    , {name:'nome' , type:'string'}
                ]
            })
        })
        // ComboBox Lojas
        this.cmbTipo_Cliente = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'clientes_combo_tipo_cliente'
            , fieldLabel     : 'Tipo Cliente'
            , hiddenName     : 'tipo_cliente'
            , triggerAction  : 'all'
            , valueField     : 'pk_tipo_cliente'
            , displayField   : 'tipo_cliente'
            , emptyText      : 'Selecione'
            , width          : 150
            , mode           : 'local'
            , allowBlank     : false
            , col            : true
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'pk_id_cliente'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe : 'Tipo_Cliente'
                    , action  : 'getTipo_ClienteCmb'
                }
                , fields:[
                    {name:'pk_tipo_cliente' , type:'int'}
                    , {name:'tipo_cliente'  , type:'string'}
                ]
            })
        })

        // Store da ComboBox Usuario
        this.cmbVendedor = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'clientes_cmb_vendedor'
            , fieldLabel     : 'Vendedor Responsavel'
            , hiddenName     : 'vendedor'
            , triggerAction  : 'all'
            , valueField     : 'id_usuario'
            , displayField   : 'Nome'
            , emptyText      : 'Selecione um Vendedor'
            , width          : 250
            //, allowBlank     : false
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'id_usuario'
                , autoDestroy : true
                , baseParams  : {
                    classe  : 'Usuarios'
                    , action : 'getUsuarioByGrupo'
                    , Grupo: 3
                }
                , fields:[
                    {name:'id_usuario'      , type:'int'}
                    , {name:'Nome' , type:'string'}
                ]
            })
            , listeners:{
/*                select:function(combo){
                    Ext.getCmp('Permissoes_Grid').getStore().load({params:{id_Usuario:combo.getValue()}})
                }*/
            }
        })

        //formulario
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:5px;'
            , border         : false
            , autoScroll     : true
            , labelAlign     : 'top'
            , items:[{
                    fieldLabel   : 'C&oacute;digo'
                    , name       : 'pk_id_cliente'
                    , xtype      : 'textfield'
                    , width      : 150
                    , readOnly   :true
                }
                , this.cmbLojas
                ,{
                    xtype        : 'e-Cmb_Tp_Pessoa'
                    , id         : 'clientes_Cmb_Tp_Pessoa'
                    , hiddenName : 'tipo'
                    , value      : 1
                    , width      : 120
                    , col        : true
                    , listeners  :{
                        scope: this
                        , 'select': function(combo) {
                            combo.value == 1 ? Ext.getCmp('dt_nascimento').enable() : Ext.getCmp('dt_nascimento').disable();
                            combo.value == 1 ? Ext.getCmp('clientes_Cmb_Sexo').enable() : Ext.getCmp('clientes_Cmb_Sexo').disable();
                            combo.value == 1 ? Ext.getCmp('profissao').enable() : Ext.getCmp('profissao').disable();
                            combo.value == 1 ? Ext.getCmp('clientes_Cmb_Estado_Civil').enable() : Ext.getCmp('clientes_Cmb_Estado_Civil').disable();

                            // Setando Mascaras Para os Campos
                            combo.value == 1 ? Ext.getCmp('clientes_CPF').setMask('999.999.999-99') : Ext.getCmp('clientes_CPF').setMask('99.999.999/9999-99');

                        }
                    }
                },{
                    fieldLabel       : 'Status'
                    , xtype          : 'combo'
                    , hiddenName     : 'status'
                    , triggerAction  : 'all'
                    , forceSelection : true
                    , width          : 120
                    , mode           : 'local'
                    , valueField     : 'Valor'
                    , displayField   : 'Label'
                    , emptyText      : 'Selecione'
                    , value          : 0
                    , store: new Ext.data.SimpleStore({
                        fields:['id', 'Valor', 'Label']
                        , data: [
                            [1, 0, 'Ativo']
                            , [2, 1, 'Inativo']
                            , [3, 2, 'Lista Negra']
                        ]
                    })
                    , col            : true
                },
                this.cmbTipo_Cliente
                , {
                layout:'column'
                , border: false
                , items:[{
                    columnWidth:.5
                    , layout: 'form'
                    , border: false
                    , labelWidth: 150
                    , items:[{
                        xtype:'fieldset'
                        //, title: 'Informa&ccedil;&otilde;es Pessoais'
                        , anchor:'99%'
                        , autoScroll : false
                        , items: [{
                            fieldLabel   : 'Nome / Raz&atilde;o social'
                            , name       : 'nome'
                            , xtype      : 'textfield'
                            , width      : 380
                            , allowBlank : false
                        },{
                            fieldLabel   : 'CPF / CNPJ'
                            , id         : 'clientes_CPF'
                            , name       : 'cpf_cnpj'
                            , xtype      : 'masktextfield'
                            , mask       : '999.999.999-99'
                            , width      : 175
                            //, allowBlank : false
                        },{
                            fieldLabel   : 'RG / Inscri&ccedil;&atilde;o Estadual'
                            , id         : 'clientes_rg'
                            , name       : 'rg_ie'
                            , xtype      : 'textfield'
                            , width      : 175
                            , col        : true
                        },{
                            fieldLabel   : 'Data Nascimento'
                            , id         : 'dt_nascimento'
                            , name       : 'dt_nascimento'
                            , xtype      : 'datefield'
                            , width      : 175
                        },{
                            fieldLabel   : 'Sexo'
                            , xtype      : 'e-Cmb_Sexo'
                            , id         : 'clientes_Cmb_Sexo'
                            , hiddenName : 'sexo'
                            , value      : 'M'
                            , width      : 175
                            , col        : true
                        },{
                            fieldLabel   : 'Profiss&atilde;o'
                            , id         : 'profissao'
                            , name       : 'profissao'
                            , xtype      : 'textfield'
                            , width      : 175
                        },{
                            fieldLabel   : 'Estado Civil'
                            , xtype      : 'e-Cmb_Estado_Civil'
                            , id         : 'clientes_Cmb_Estado_Civil'
                            , hiddenName : 'estado_civil'
                            , value      : 1
                            , width      : 175
                            , col        : true
                        }]
                    },{
                        xtype:'fieldset'
                        //, title: 'Contatos'
                        , anchor:'99%'
                        , autoScroll : false
                        , items: [{
                            fieldLabel: 'Telefone Fixo'
                            , xtype   : 'masktextfield'
                            , name    : 'telefone_fixo'
                            , width   : 175
                            , mask    : '(99) 9999-9999'
                        },{
                            fieldLabel: 'Telefone Movel'
                            , xtype   : 'masktextfield'
                            , name : 'telefone_movel'
                            , width: 175
                            , col  : true
                            , mask    : '(99) 9999-9999'
                        },{
                            fieldLabel: 'E-mail'
                            , xtype: 'textfield'
                            , name : 'email'
                            , width: 380
                        }]
                    }]
                },{
                    columnWidth:.5
                    , layout: 'form'
                    , border: false
                    , labelWidth: 120
                    , items:[{
                        xtype:'fieldset'
                        //, title: 'Endere&ccedil;o'
                        , anchor:'99%'
                        , autoScroll : false
                        , items: [{
                            name       : 'id_endereco'
                            , xtype      : 'hidden'
                        },{
                            fieldLabel   : 'Rua'
                            , name       : 'rua'
                            , xtype      : 'textfield'
                            , labelWidth : 50
                            , width      : 270
                            , allowBlank : false
                        },{
                            fieldLabel   : 'N&uacute;mero'
                            , name       : 'numero'
                            , xtype      : 'textfield'
                            , labelWidth : 50
                            , width      : 80
                            , col        : true
                        },{
                            fieldLabel   : 'Bairro'
                            , name       : 'bairro'
                            , xtype      : 'textfield'
                            , labelWidth : 50
                            , width      : 120
                            , allowBlank : false
                        },{
                            fieldLabel   : 'Cidade'
                            , name       : 'cidade'
                            , xtype      : 'textfield'
                            , labelWidth : 50
                            , width      : 150
                            , col        : true
                            , allowBlank : false
                        },{
                            xtype        : 'e-Cmb_Uf'
                            , id         : 'clientes_cmb_uf'
                            , hiddenName : 'uf'
                            , width      : 50
                            , emptyText  : ''
                            , value      : 'RJ'
                            , col        : true
                        },{
                            fieldLabel   : 'Complemento'
                            , name       : 'complemento'
                            , xtype      : 'textfield'
                            , labelWidth : 50
                            , width      : 120
                        },{
                            fieldLabel   : 'CEP'
                            , name       : 'cep'
                            , xtype      : 'masktextfield'
                            , labelWidth : 50
                            , width      : 100
                            , mask       : '99.999-999'
                            , col        : true
                        },{
                            xtype        : 'e-Cmb_Tp_Endereco'
                            , id         : 'clientes_Cmb_Tp_Endereco'
                            , hiddenName : 'tipo_endereco'
                            , col        : true
                            , value      : 1
                        }]
                    }
                    ,{
                        xtype:'fieldset'
                        , anchor:'99%'
                        , autoScroll : false
                        , items: [
                        this.cmbVendedor
                        , {
                            xtype: 'textarea'
                            , fieldLabel: 'Observa&ccedil;&otilde;es'
                            , name   : 'observacoes'
                            , width  : 380
                            , height : 70
                        }]
                    }]
                }]
            }]
        })

        // Informacoes adicionais na botton bar
        var data_inclusao  = new Ext.Toolbar.TextItem({id: 'dt_inclusao', text: '00/00/0000 00:00:00', width: 110});
        var data_alteracao = new Ext.Toolbar.TextItem({id: 'dt_alteracao', text: '00/00/0000 00:00:00', width: 110});

        //Ext.fly(date.getEl()).update(new Date().format('d/n/Y'));
        Ext.apply(this,{
            items  : this.formPanel
            , bbar : [
            '-',' Data Inclus&atilde;o: '
            , data_inclusao
            , '-',' Data Altera&ccedil;&atilde;o: '
            , data_alteracao
            , '-'
            ]
            , tbar : [
            this.btnSalvar = new Ext.Button({
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
        Clientes_Form.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Clientes_Form.superclass.initEvents.call(this);

        this.on('beforeshow', this.onBeforeShow, this);

    }
    , onBeforeShow: function(){
        Ext.getCmp('clientes_cmb_vendedor').store.load();
    }

    /*
     * Sobreescrevo o metodo show de Ext.Window para aplicar a seguinte rotina:
     * Se IdRegistro foi informado, carrega o form, senao, reseta o form.
     */
    , show: function() {
        Clientes_Form.superclass.show.apply(this,arguments);
        this.formPanel.getForm().reset();
        // Zerando as Datas
        var dt_inclusao  = Ext.getCmp('dt_inclusao').setText('00/00/0000 00:00:00');
        var dt_alteracao = Ext.getCmp('dt_alteracao').setText('00/00/0000 00:00:00');

        //se tem usuario
        if(this.IdRegistro !== 0) {
            this.btnExcluir.show();
            // Tratamento de Permissoes
            // o usuario pode excluir?
            if(this.perm_delete == 0){
                this.btnExcluir.setVisible(false)
            }
            // o usuario pode alterar?
            if(this.perm_update == 0){
                this.btnSalvar.setVisible(false);
            }

            this.el.mask('Carregando informa&ccedil;&otilde;es');
            Ext.getCmp('main_statusbar').msg('load');

            /*
             * Carregando o formulario. Ele deve respeitar algums formatos especificiados na documentacao ext de
             * Ext.form.Action.Load, como por exemplo conter uma propriedade success e data.
             */
            this.formPanel.getForm().load({
                url : 'main.php'
                , params : {
                    classe   : 'Clientes'
                    , action : 'getCliente'
                    , pk_id_cliente : this.IdRegistro
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
                this.btnSalvar.setVisible(false);
            }
            // Resetando o formulario
            this.formPanel.getForm().reset();

        }
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Clientes_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
    }

    // Listeners

    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;

        // Mostrando as Datas
        var dt_inclusao  = Ext.getCmp('dt_inclusao').setText(data.dt_inclusao);
        var dt_alteracao = Ext.getCmp('dt_alteracao').setText(data.dt_alteracao);

        Ext.getCmp('clientes_combo_Lojas').setValue(0);

        // Setando Mascaras Para os Campos
        data.tipo == 1 ? Ext.getCmp('clientes_CPF').setMask('999.999.999-99') : Ext.getCmp('clientes_CPF').setMask('99.999.999/9999-99');
        Ext.getCmp('clientes_CPF').setValue(data.cpf_cnpj);


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
            url: 'main.php'
            , params: {
                classe   : 'Clientes'
                , action: 'CriaAtualiza'
                , id    : this.IdRegistro
            }
            , scope:this
            //ao terminar de submitar
            , success: function() {
                //tiro mascara
                this.el.unmask();
                Ext.getCmp('main_statusbar').msg('save');
                //esconde janela
                this.hide();
                this.fireEvent('salvar',this);
            }
            , failure: function(form , action){
                var obj = Ext.decode(action.response.responseText);
                //alert(msg);
                this.el.unmask();
                this.hide();
                Ext.getCmp('main_statusbar').msg('error');
                //Ext.Msg.alert('Erro', obj.msg );
                Ext.MessageBox.show({ title:'Desculpe!', msg: obj.msg + "<br>C&oacute;d: " + obj.code, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
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
                    url    : 'main.php'
                    , params : {
                        classe : 'Clientes'
                        , action: 'deleteClientes'
                        , 'pk_id_cliente[]': this.IdRegistro
                    }
                    ,scope  : this
                    ,success: function(r, o) {
                        var obj = Ext.decode(r.responseText);
                        if(obj.success){
                            this.el.unmask();
                            this.hide();
                            // Evento personalizado excluir sendo disparado
                            this.fireEvent('excluir',this);
                            Ext.getCmp('main_statusbar').msg('ok');
                        }
                        else {
                            this.el.unmask();
                            this.hide();
                            // Evento personalizado excluir sendo disparado
                            this.fireEvent('excluir',this);
                            Ext.MessageBox.show({ title:'Falha', msg: obj.msg+"<br> C&oacute;d: " + obj.code, buttons: Ext. MessageBox.OK, icon: Ext.MessageBox.WARNING });
                            Ext.getCmp('main_statusbar').msg('error');
                        }
                    }
                });
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




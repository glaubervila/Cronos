/**
 * @package     : Produtos
 * @name        : Produtos_Form
 * @Diretorio   : Main/Modulos/Produtos/
 * @date        : 31/03/2011
 */

var Produtos_Form = Ext.extend(Ext.Window,{


    id: 'Produtos_Form'
    , identificacao: '5001' // Identificacao para permissoes

    , main_url     : 'main.php'
    , main_class   : 'Produtos'
    , pk_id        : 'pk_id_produto'
    , metodo_load  : 'load_produto_id'
    , metodo_delete: 'delete_Produtos'
    , metodo_submit: 'criaAtualiza'

    , IdRegistro   : 0

    , modal        : true
    , constrain    : true
    , maximizable  : true
    , width        : 600
    , height       : 500
    , title        : 'Cadastro de Produtos'
    , layout       : 'fit'
    , autoScroll   : true

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

        Produtos_Form.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';

        this.cmbCategoria = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'combo_Categoria'
            , fieldLabel     : 'Categoria'
            , hiddenName     : 'fk_id_categoria'
            , triggerAction  : 'all'
            , valueField     : 'pk_id_categoria'
            , displayField   : 'categoria'
            , emptyText      : 'Selecione uma Categoria'
            , width          : 210
            , mode           : 'local'
            , allowBlank     : false
            , store          : new Ext.data.JsonStore({
                url           : this.main_url
                , root        : 'rows'
                , idProperty  : 'pk_id_categoria'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe   : 'Categoria'
                    , action : 'getCategoriasCmb'
                }
                , fields:[
                    {name:'pk_id_categoria', type:'int'}
                    , {name:'categoria'    , type:'string'}
                ]
            })
        })

        // ComboBox Tributacao
        this.cmbTributacao = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'combo_Tributacao'
            , fieldLabel     : 'Tributacao'
            , hiddenName     : 'tributacao'
            , triggerAction  : 'all'
            , valueField     : 'pk_id_tributacao'
            , displayField   : 'tributacao'
            , emptyText      : 'Selecione uma Tributacao'
            , width          : 120
            , mode           : 'local'
            //, allowBlank     : false
            , store          : new Ext.data.JsonStore({
                url           : this.main_url
                , root        : 'rows'
                , idProperty  : 'pk_id_tributacao'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe   : 'Tributacao'
                    , action : 'load_tributacao_cmb'
                }
                , fields:[
                    {name:'pk_id_tributacao', type:'int'}
                    , {name:'tributacao'    , type:'string'}
                ]
            })
        })


        this.foto_produto = new Ext.BoxComponent({
            autoEl: {
                tag: 'img'
                , src: 'Resources/default_image_150x110.png'
                , id: 'Foto_produto'
                , width: 150
                , height:110
            }
        })

        this.cmbGarantia = new Ext.form.ComboBox({
            fieldLabel       : 'Garantia'
            , xtype          : 'combo'
            , hiddenName     : 'garantia'
            , triggerAction  : 'all'
            , forceSelection : true
            , width          : 120
            , mode           : 'local'
            , valueField     : 'Valor'
            , displayField   : 'Valor'
            , emptyText      : 'Selecione'
            , col            : true
            , store: new Ext.data.SimpleStore({
                fields:['id', 'Valor']
                , data: [
                    [1, '3 Dias']
                    , [2, '7 Dias']
                    , [3, '15 Dias']
                    , [4, '30 Dias']
                    , [5, '60 Dias']
                    , [6, '90 Dias']
                    , [7, '1 Ano']
                    , [8, '2 Anos']
                    , [9, '3 Anos']
                ]
            })
        })

        //formulario
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:2px;'
            , border         : false
            , autoScroll     : true
            , labelAlign     : 'top'
            , items:[{
                xtype   :'fieldset'
                , anchor:'99%'
                , border: true
                , style:{
                    padding:'5px'
                }
                , items:[{
                    xtype   :'fieldset'
                    , border: true
                    , items:[{
                        fieldLabel   : 'Url Imagem'
                        , name       : 'url_image'
                        , id         : 'txt_url_image'
                        //, xtype      : 'textfield'
                        , xtype      : 'hidden'
                    },{
                        fieldLabel   : 'Name Imagem'
                        , name       : 'name_image'
                        , id         : 'txt_name_image'
                        //, xtype      : 'textfield'
                        , xtype      : 'hidden'
                    },{
                        fieldLabel   : 'Código Interno'
                        , name       : 'pk_id_produto'
                        , xtype      : 'textfield'
                        , width      : 100
                        , readOnly  : true
                    },{
                        fieldLabel   : 'Descrição Curta'
                        , name       : 'descricao_curta'
                        , xtype      : 'textfield'
                        , width      : 275
                        , minLength  : 3
                        , maxLength  : 40
                        , allowBlank : false
                        , col        : true
                    }]
                },{
                    xtype   :'fieldset'
                    , width :250
                    , border: true
                    , style:{
                        //padding:'0px'
                    }
                    , items:[{
                        fieldLabel   : 'Descricao Longa'
                        , name       : 'descricao_longa'
                        , xtype      : 'textarea'
                        , width      : 210
                        , height     : 40
                        , minLength  : 3
                        , maxLength  : 255
                        , allowBlank : false
                    },this.cmbCategoria]
                },{
                    xtype   :'fieldset'
                    , width :100
                    , border: true
                    , style:{
                        //padding:'0px'
                    }
                    , col  : true
                    , items:[{
                        fieldLabel   : 'Preço'
                        , name       : 'preco'
                        , xtype      : 'textfield'
                        , width      : 75
                        , readOnly  : true
                    },{
                        fieldLabel   : 'Quantidade'
                        , name       : 'quantidade'
                        , xtype      : 'textfield'
                        , width      : 75
                        , readOnly  : true
                    }]
                },{
                    xtype   :'fieldset'
                    , width :150
                    , height:140
                    , border: true
                    , style:{
                        padding:'0px'
                    }
                    , col  : true
                    , items:[
                        this.foto_produto
                        , {
                            xtype    :'button'
                            , id     : 'btn_foto'
                            , text   : 'Selecione Uma Foto'
                            , width  : 148
                            , height : 28
                            , iconCls: 'silk-picture-add'
                            , scope  : this
                            , handler: this.onBtnFotoClick
                        }
                    ]
                },{
                    xtype   :'fieldset'
                    , border: true
                    , style:{
                        //padding:'0px'
                    }
                    , items:[

                        this.cmbTributacao
                        , {
                            xtype        : 'e-Cmb_Unidade'
                            , id         : 'produtos_Cmb_Unidade'
                            , hiddenName : 'unidade'
                            , width      : 120
                            , col        : true
                        }
                        ,this.cmbGarantia
                    ]
                }]
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
        Produtos_Form.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Produtos_Form.superclass.initEvents.call(this);

    }

    , onBtnFotoClick: function(){
        this.cria_winUpload();
        this.winUploadFoto.show();
    }


    , mostrar_imagem:function(url_image){
            var foto_produto = Ext.get('Foto_produto');
            foto_produto.set({src: url_image});
    }
    , limpar_imagem:function(){
            var foto_produto = Ext.get('Foto_produto');
            foto_produto.set({src: 'Resources/default_image_150x150.png'});
    }
    , onUploadFile:function(obj,data){
        //alert(data.arquivo);
        // Colocar o nome do arquivo no input hidden
        Ext.getCmp('txt_url_image').setValue(data.url_image);
        Ext.getCmp('txt_name_image').setValue(data.name_image);

        this.mostrar_imagem(data.url_image);

    }

    , cria_winUpload: function(){
            this.winUploadFoto = new FileUploads({
                id: 'winUploadFotoProdutos'
                , listeners  :{
                    scope  : this
                    , uploadsuccess : this.onUploadFile
                }
            });

            return this.winUploadFoto;
    }

    /*
     * Sobreescrevo o metodo show de Ext.Window para aplicar a seguinte rotina:
     * Se IdRegistro foi informado, carrega o form, senao, reseta o form.
     */
    , show: function() {
        Produtos_Form.superclass.show.apply(this,arguments);
        this.formPanel.getForm().reset();
        this.limpar_imagem();
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
                    , 'pk_id_produto': this.IdRegistro
                }
                , scope: this
                , success: this._onFormLoad
                , failure: this._onFormLoadFailure
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
            this.limpar_imagem();

        }
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Produtos_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
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

    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;

        // Carregando Imagem do Produto
        if (data.url_image) {
            this.mostrar_imagem(data.url_image);
        }


//         this.cod_ean_Grid.store.baseParams.pk_id_produto = data.pk_id_produto;
//         this.cod_ean_Grid.store.load();

        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();
    }

    , _onFormLoadFailure: function(form, request) {
        //alert('nao foi possivel carregar o registro');
        var obj = Ext.decode(request.response.responseText);
        this.el.unmask();
        this.hide();
        Ext.getCmp('main_statusbar').msg('error');
        Ext.MessageBox.show({ title:'Desculpe!', msg: obj.msg + "<br>C&oacute;d: " + obj.code, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });

        this.el.unmask();
        this.hide();
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
                , 'pk_id_produto' : this.IdRegistro
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
            , failure: function(form , action){
                var obj = Ext.decode(action.response.responseText);
                this.el.unmask();
                this.hide();
                Ext.getCmp('main_statusbar').msg('error');
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
                    url: this.main_url
                    , params: {
                        classe   : this.main_class
                        , action : this.metodo_delete
                        , 'pk_id_produto' : this.IdRegistro
                    }
                    , scope: this
                    , success: function() {
                        this.el.unmask();
                        Ext.getCmp('main_statusbar').msg('ok');
                        this.hide();
                        // Evento personalizado excluir sendo disparado
                        this.fireEvent('excluir',this);
                    }
                    , failure: function(form , action){
                        var obj = Ext.decode(action.response.responseText);
                        this.el.unmask();
                        Ext.getCmp('main_statusbar').msg('error');
                        this.hide();
                        Ext.MessageBox.show({ title:'Desculpe!', msg: obj.msg + "<br>C&oacute;d: " + obj.code, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
                    }
                })
            }
        },this)
    }



});
Ext.reg('e-Produtos_Form', Produtos_Form);



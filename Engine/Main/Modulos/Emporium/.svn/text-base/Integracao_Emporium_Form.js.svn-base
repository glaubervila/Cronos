/**
 * @package     : Emporium
 * @name        : Integracao_Emporium_Form
 * @version     : 1.0
 * @Diretorio   : Main/Modulos/Emporium/
 * @date        : 09/12/2010
 */
var Integracao_Emporium_Form = Ext.extend(Ext.Window,{

    id: 'Integracao_Emporium_Form'
    , identificacao : '6001' // Identificacao para permissoes

    , main_url     : 'main.php'
    , main_class   : 'Integracao_Emporium'
    , pk_id        : 'pk_id_integracao'
    , metodo_load  : 'load_integracao'
    , metodo_delete: 'delete_integracoes'

    , IdRegistro: 0

    , modal  : true
    , constrain: true
    //, maximizable: true
    , width  : 400
    , height : 360
    , title  : 'Detalhes de Integracao'
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

        Integracao_Emporium_Form.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';


        // ComboBox Status
        this.cmbStatus = new Ext.form.ComboBox({
            root             : 'rows'
            , id             : 'cmb_status'
            , fieldLabel     : 'Status'
            , readOnly       : true
            , hiddenName     : 'status'
            , valueField     : 'pk_id_status'
            , displayField   : 'status'
            , width          : 200
            , mode           : 'local'
            , store          : new Ext.data.JsonStore({
                url           : 'main.php'
                , root        : 'rows'
                , idProperty  : 'pk_id_status'
                , autoLoad    : true
                , autoDestroy : true
                , baseParams  : {
                    classe : 'Common'
                    , action  : 'getStatus'
                }
                , fields:[
                    {name:'pk_id_status', type:'int'}
                    , {name:'status'    , type:'string'}
                ]
            })
        })

        this.cmbTipo = new Ext.form.ComboBox({
            fieldLabel       : 'Tipo'
            , xtype          : 'combo'
            , hiddenName     : 'tipo'
            , width          : 200
            , readOnly       : true
            , mode           : 'local'
            , valueField     : 'id'
            , displayField   : 'Valor'
            , store: new Ext.data.SimpleStore({
                fields:['id', 'Valor']
                , data: [
                    [1,  'Importação']
                    , [2,'Exportação']
                ]
            })
        })

        //formulario
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:10px;'
            , border         : false
            , autoScroll     : true
            //, labelAlign     : 'top'
            , items:[{
                fieldLabel   : 'C&oacute;digo'
                , name       : 'pk_id_integracao'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            }
            , this.cmbStatus
            , this.cmbTipo
            ,{
                fieldLabel   : 'Entidade'
                , name       : 'entidade'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            },{
                fieldLabel   : 'Data Inicio'
                , name       : 'dt_inicio'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            },{
                fieldLabel   : 'Data Termino'
                , name       : 'dt_termino'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            },{
                fieldLabel   : 'Usu&aacute;rio'
                , name       : 'usuario'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            },{
                fieldLabel   : 'Total Registros'
                , name       : 'total'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            },{
                fieldLabel   : 'Total Erros'
                , name       : 'total_erros'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            },{
                fieldLabel   : 'Arquivo BackUp'
                , name       : 'arquivo'
                , xtype      : 'textfield'
                , width      : 200
                , readOnly   :true
            }]
        })

        Ext.apply(this,{
            items  : this.formPanel
            , bbar : ['->'
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
        Integracao_Emporium_Form.superclass.initComponent.call(this);
    }

    /*
     * Sobreescrevo o metodo show de Ext.Window para aplicar a seguinte rotina:
     * Se IdRegistro foi informado, carrega o form, senao, reseta o form.
     */
    , show: function() {
        Integracao_Emporium_Form.superclass.show.apply(this,arguments);
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
                    , 'pk_id_integracao': this.IdRegistro
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
        Integracao_Emporium_Form.superclass.onDestroy.apply(this,arguments);
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
                        , 'pk_id_integracao' : this.IdRegistro
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
        this.hide();
    }
});
/**
 * @package     : Emporium
 * @name        : Exportacao_Produtos_Emporium_Form
 * @Diretorio   : Main/Modulos/Emporium/
 * @date        : 07/02/2011
 * @Dependencias:
 * - Main/Modulos/Emporium/Integracao_Emporium.class.php
 * - Main/Modulos/Lojas/Lojas.class.php
 */
var Exportacao_Produtos_Emporium_Form = Ext.extend(Ext.Panel,{

    id: 'Exportacao_Produtos_Emporium_Form'
    , identificacao : '6003' // Identificacao para permissoes

    , main_url   : 'main.php'
    , main_class : 'Exportacao_Produtos_Emporium'
    , pk_id      : 'pk_id_integracao'
    , metodo_submit: 'Exporta_Produtos'


    //, modal  : true
    , constrain: true
    //, maximized  : true
    //, width  : 400
    //, height : 250
    //, title  : ''
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

        Exportacao_Produtos_Emporium_Form.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {
        //Ext.QuickTips.init();
        // turn on validation errors beside the field globally
        Ext.form.Field.prototype.msgTarget = 'side';

        //formulario
        this.formPanel = new Ext.form.FormPanel({
            bodyStyle: 'padding:10px;'
            , border         : false
            , autoScroll     : true
            , labelAlign     : 'top'
            , items:[{
                xtype:'fieldset'
                , title: 'Opcionais'
                , anchor:'99%'
                , width: 400
                , autoScroll : false
                , items: [{
                    xtype: 'checkbox'
                    , name: 'compactar'
                    , hideLabel :true
                    , boxLabel  : 'Compactar arquivo (.zip)'
                    , checked   : true
                },{
                    xtype: 'checkbox'
                    , name      : 'backup'
                    , hideLabel :true
                    , boxLabel  : 'Guardar Copia no Servidor'
                    , checked   : true
                }]
            }]
        })

        Ext.apply(this,{
            items  : this.formPanel
            , tbar : [
            this.btnExportar = new Ext.Button({
                text     : 'Exportar'
                , iconCls: 'silk-database-go'
                , scope  : this
                , handler: this._onBtnExportarClick
            })]
        })
        Exportacao_Produtos_Emporium_Form.superclass.initComponent.call(this);
    }
    , show: function() {
        Exportacao_Produtos_Emporium_Form.superclass.show.apply(this,arguments);
        //this.formPanel.getForm().reset();
        if(this.perm_execut == 0){
             this.btnExportar.disable();;
        }
        //this.formPanel.getForm().reset();
    }

    // Limpa variaveis alocadas
    , onDestroy: function() {
        Exportacao_Produtos_Emporium_Form.superclass.onDestroy.apply(this,arguments);
        this.formPanel = null;
        Ext.getCmp('main_statusbar').clearStatus();
    }

    // Listeners
    // Listener disparado ao carregar o formulario
    , _onFormLoad: function(form, request) {
        var data = request.result.data;
        // tiro a mascara
        this.el.unmask();
        Ext.getCmp('main_statusbar').clearStatus();
    }

    , _onBtnExportarClick: function() {
        //pego o formulario
        var form = this.formPanel.getForm();
        //verifico se e valido
        if(!form.isValid()) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            return false;
        }
        // crio uma mascara
        //this.el.mask('Enviando Solicita&ccedil;&atilde;o');
        Ext.getCmp('main_statusbar').msg('job');

        // Aumentando o tempo de espera do EXT
        Ext.Ajax.timeout = 9999999999;
        // Mostrando uma mensagem de espera
        Ext.MessageBox.show({
            msg: 'Gerando Arquivos de Exportação, isto pode levar alguns minutos.<br>Está operação não pode ser interrompida!<br>Por favor aguarde...'
            , progressText: 'Exportando...'
            , width    : 400
            , wait     : true
            , interval : 50
            , increment: 5

        });

        var form_values = form.getValues();

        Ext.Ajax.request({
            url : this.main_url
            , params    : {
                classe   : this.main_class
                , action: this.metodo_submit
                , 'compactar': form_values.compactar
                , 'backup'   : form_values.backup
            }
            //, waitMsg: 'Por favor espere...'
            , scope : this
            , success: function( r, o ){
                var obj = Ext.decode(r.responseText);
                Ext.getCmp('main_statusbar').msg('ok');

                Ext.Msg.alert('Status', 'Exportação concluida com Sucesso!<br>Total à Exportar: ' + obj.total_a_exportar+'<br>Total Exportado '+obj.total_exportados+'<br> Click no Link para baixar o arquivo<br><a href = "Main/PHP/Download_Arquivo.php?file='+obj.file+'&path='+obj.path+'" target="_blank">Download Arquivo</a></center>');
             }
            , failure: function(r,o) {

                Ext.getCmp('main_statusbar').msg('error');

                Ext.MessageBox.show({ title:'Status Exportação', msg: 'Nenhum Registro foi exportado<br>Descricão:<br> ' + obj.msg , buttons: Ext. MessageBox.OK, icon: Ext.MessageBox.WARNING });
            }
        });

    }
});
Ext.reg('e-Exportacao_Produtos_Emporium_Form', Exportacao_Produtos_Emporium_Form);
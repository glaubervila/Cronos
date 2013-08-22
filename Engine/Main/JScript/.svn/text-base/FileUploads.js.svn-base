var FileUploads = new Ext.extend(Ext.Window,{

    id: 'FileUploads_Window'
    , constrain  : true
    , width      : 340
    , height     : 150
    , title      : 'Upload de Arquivo'
    , layout     : 'fit'
    , autoScroll : false


    //, main_url     : 'Main/PHP/ImageUpload.class.php'
    , main_url     : 'main.php'
    , main_class   : 'FileUpload'
    , metodo_submit: 'upload_imagem'


    , constructor: function() {

        this.addEvents({
            uploadsuccess: true
        });

        FileUploads.superclass.constructor.apply(this,arguments);
    }

    , initComponent: function() {

        this.form_upload = new Ext.form.FormPanel({
            id: 'Form_Upload'
            , bodyStyle: 'padding:10px;'
            , border         : false
            , autoScroll     : false
            , labelAlign     : 'top'
            , fileUpload     : true
            , items:[{
                xtype        :'fileuploadfield'
                , name       :'foto'
                , fieldLabel :'Escolha um Arquivo'
                , width      : 300
                , allowBlank : false
                , buttonText : ''
                , buttonCfg: {
                    iconCls: 'silk-folder-page'
                }
            }]
        })

        Ext.apply(this,{
            items:[
                this.form_upload
            ]
            , bbar : ['->'
            , this.btnEnviar = new Ext.Button({
                text     : 'Enviar Imagem'
                , iconCls: 'silk-add'
                , scope  : this
                , handler: this.onBtnEnviarClick
            })]

        })
        FileUploads.superclass.initComponent.call(this);
    }

     , onBtnEnviarClick: function(){

        if(!this.form_upload.getForm().isValid()) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Preencha corretamente todos os campos!');
            return false;
        }
        this.form_upload.getForm().submit({
            url: this.main_url
            , method: 'POST'
            , scope: this
            , params: {
                classe       : this.main_class
                , action     : this.metodo_submit
            }
            , success: function(form, action){
                var json = Ext.decode(action.response.responseText);
                var data = json.data;
                this.fireEvent('uploadsuccess', this, data);
                this.close();
            }
            , failure: function(form , action){
                var json = Ext.decode(action.response.responseText);
                var obj  = json.data;
                Ext.MessageBox.show({ title:'Desculpe!', msg: obj.msg, buttons: Ext. MessageBox.OK, icon:  Ext.MessageBox.WARNING });
            }
        });

    }

});

Ext.reg('e-FileUpload', FileUploads);
Ext.onReady(function(){

    Ext.QuickTips.init();

    Ext.override(Ext.form.Field, {
        fieldLabelOriginal: '',

        initComponent : function(){
            Ext.form.Field.superclass.initComponent.call(this);
            this.addEvents(

                'focus',

                'blur',

                'specialkey',

                'change',

                'invalid',

                'valid'
            );

            var label = this.fieldLabel;
            var labelSeparator = this.labelSeparator ? this.labelSeparator : ':';

            this.fieldLabelOriginal = label + labelSeparator;

            if(!this.allowBlank && !this.disabled)
                this.fieldLabel = "<span style='color:#F00;'>*</span>" + label;

            this.on('render', function(){
                if(this.disabled)
                    this.label.update('<span style="color:#ccc;">' + this.fieldLabelOriginal + '</span>');
            },this);

            this.on('enable', function(){
                var labelEnable = this.allowBlank ? label : "<span style='color:#F00;'>*</span>" + this.fieldLabelOriginal;
                this.label.update(labelEnable);

            },this);

            this.on('disable', function(){
                if(this.rendered)
                    this.label.update('<span style="color:#ccc;">' + this.fieldLabelOriginal + '</span>');
            },this);
        },

        setAllowBlank: function(v){
            var label = this.fieldLabelOriginal;

            this.allowBlank = v;
            var newLabel = (!this.allowBlank && !this.disabled) ? "<span style='color:#F00;'>*</span>" + label : label;

            if(this.label)
                this.label.update(newLabel);
        }
    })

    var arrOpcoes = [
        [1, 'Masculino'],
        [2, 'Feminino']
    ];

    var combo = new Ext.form.ComboBox({
        fieldLabel: 'Sexo',
        triggerAction: 'all',
        store: arrOpcoes,
        mode: 'local',
        forceSelection: true,
        allowBlank: false
    })

    var campo = new Ext.form.TextField({
        fieldLabel: 'Nome',
        disabled: true
    })

    var campo2 = new Ext.form.TextField({
        fieldLabel: 'Endereço'
    })

    var arrOpcoesDinamico = [
        [1, 'Habilitado'],
        [2, 'Desabilitado'],
        [3, 'Obrigatório'],
        [4, 'Não Obrigatório'],
        [5, 'Obrigatório Habilitado'],
        [6, 'Não Obrigatório Habilitado'],
        [7, 'Obrigatório Desabilitado'],
        [8, 'Não Obrigatório Desabilitado']
    ];

    var comboDinamico = new Ext.form.ComboBox({
        fieldLabel: 'Alterar',
        triggerAction: 'all',
        store: arrOpcoesDinamico,
        mode: 'local',
        forceSelection: true,
        listeners: {
            select: function(c){
                switch(c.getValue()){
                case 1:
                    campoDinamico.setAllowBlank(true);
                    campoDinamico.enable();

                    cbgDinamico.setAllowBlank(true);
                    cbgDinamico.enable();

                    cbDinamico.setAllowBlank(true);
                    cbDinamico.enable();

                    dateDinamico.setAllowBlank(true);
                    dateDinamico.enable();

                    timeDinamico.setAllowBlank(true);
                    timeDinamico.enable();

                    numberDinamico.setAllowBlank(true);
                    numberDinamico.enable();

                    rgDinamico.setAllowBlank(true);
                    rgDinamico.enable();

                    textAreaDinamico.setAllowBlank(true);
                    textAreaDinamico.enable();

                    break;
                case 2:
                    campoDinamico.setAllowBlank(true);
                    campoDinamico.disable();

                    cbgDinamico.setAllowBlank(true);
                    cbgDinamico.disable();

                    cbDinamico.setAllowBlank(true);
                    cbDinamico.disable();

                    dateDinamico.setAllowBlank(true);
                    dateDinamico.disable();

                    timeDinamico.setAllowBlank(true);
                    timeDinamico.disable();

                    numberDinamico.setAllowBlank(true);
                    numberDinamico.disable();

                    rgDinamico.setAllowBlank(true);
                    rgDinamico.disable();

                    textAreaDinamico.setAllowBlank(true);
                    textAreaDinamico.disable();

                    break;
                case 3:
                    campoDinamico.setAllowBlank(false);
                    campoDinamico.enable();

                    cbgDinamico.setAllowBlank(false);
                    cbgDinamico.enable();

                    cbDinamico.setAllowBlank(false);
                    cbDinamico.enable();

                    dateDinamico.setAllowBlank(false);
                    dateDinamico.enable();

                    timeDinamico.setAllowBlank(false);
                    timeDinamico.enable();

                    numberDinamico.setAllowBlank(false);
                    numberDinamico.enable();

                    rgDinamico.setAllowBlank(false);
                    rgDinamico.enable();

                    textAreaDinamico.setAllowBlank(false);
                    textAreaDinamico.enable();

                    break;
                case 4:
                    campoDinamico.setAllowBlank(true);
                    campoDinamico.enable();

                    cbgDinamico.setAllowBlank(true);
                    cbgDinamico.enable();

                    cbDinamico.setAllowBlank(true);
                    cbDinamico.enable();

                    dateDinamico.setAllowBlank(true);
                    dateDinamico.enable();

                    timeDinamico.setAllowBlank(true);
                    timeDinamico.enable();

                    numberDinamico.setAllowBlank(true);
                    numberDinamico.enable();

                    rgDinamico.setAllowBlank(true);
                    rgDinamico.enable();

                    textAreaDinamico.setAllowBlank(true);
                    textAreaDinamico.enable();

                    break;
                case 5:
                    campoDinamico.setAllowBlank(false);
                    campoDinamico.enable();

                    cbgDinamico.setAllowBlank(false);
                    cbgDinamico.enable();

                    cbDinamico.setAllowBlank(false);
                    cbDinamico.enable();

                    dateDinamico.setAllowBlank(false);
                    dateDinamico.enable();

                    timeDinamico.setAllowBlank(false);
                    timeDinamico.enable();

                    numberDinamico.setAllowBlank(false);
                    numberDinamico.enable();

                    rgDinamico.setAllowBlank(false);
                    rgDinamico.enable();

                    textAreaDinamico.setAllowBlank(false);
                    textAreaDinamico.enable();

                    break;
                case 6:
                    campoDinamico.setAllowBlank(true);
                    campoDinamico.enable();

                    cbgDinamico.setAllowBlank(true);
                    cbgDinamico.enable();

                    cbDinamico.setAllowBlank(true);
                    cbDinamico.enable();

                    dateDinamico.setAllowBlank(true);
                    dateDinamico.enable();

                    timeDinamico.setAllowBlank(true);
                    timeDinamico.enable();

                    numberDinamico.setAllowBlank(true);
                    numberDinamico.enable();

                    rgDinamico.setAllowBlank(true);
                    rgDinamico.enable();

                    textAreaDinamico.setAllowBlank(true);
                    textAreaDinamico.enable();

                    break;
                case 7:
                    campoDinamico.setAllowBlank(false);
                    campoDinamico.disable();

                    cbgDinamico.setAllowBlank(false);
                    cbgDinamico.disable();

                    cbDinamico.setAllowBlank(false);
                    cbDinamico.disable();

                    dateDinamico.setAllowBlank(false);
                    dateDinamico.disable();

                    timeDinamico.setAllowBlank(false);
                    timeDinamico.disable();

                    numberDinamico.setAllowBlank(false);
                    numberDinamico.disable();

                    rgDinamico.setAllowBlank(false);
                    rgDinamico.disable();

                    textAreaDinamico.setAllowBlank(false);
                    textAreaDinamico.disable();

                    break;
                case 8:
                    campoDinamico.setAllowBlank(true);
                    campoDinamico.disable();

                    cbgDinamico.setAllowBlank(true);
                    cbgDinamico.disable();

                    cbDinamico.setAllowBlank(true);
                    cbDinamico.disable();

                    dateDinamico.setAllowBlank(true);
                    dateDinamico.disable();

                    timeDinamico.setAllowBlank(true);
                    timeDinamico.disable();

                    numberDinamico.setAllowBlank(true);
                    numberDinamico.disable();

                    rgDinamico.setAllowBlank(true);
                    rgDinamico.disable();

                    textAreaDinamico.setAllowBlank(true);
                    textAreaDinamico.disable();

                    break;
                }
            }
        }
    })

    var campoDinamico = new Ext.form.TextField({
        fieldLabel: 'Dinâmico'
    })

    var cbgDinamico = new Ext.form.CheckboxGroup({
        fieldLabel: 'Dinâmico',
        width: 200,
        items: [{
            boxLabel: 'Opção 1'
        },{
            boxLabel: 'Opção 2'
        }]
    });

    var cbDinamico = new Ext.form.ComboBox({
        fieldLabel: 'Dinâmico',
        triggerAction: 'all',
        store: [[1, 'Opção 1'], [2, 'Opção 2']],
        mode: 'local',
        forceSelection: true
    });

    var dateDinamico = new Ext.form.DateField({
        fieldLabel: 'Dinâmico'
    })

    var timeDinamico = new Ext.form.TimeField({
        fieldLabel: 'Dinâmico'
    })

    var numberDinamico = new Ext.form.NumberField({
        fieldLabel: 'Dinâmico'
    })

    var rgDinamico = new Ext.form.RadioGroup({
        fieldLabel: 'Dinâmico',
        width: 200,
        items: [{
            boxLabel: 'Opção 1'
        },{
            boxLabel: 'Opção 2'
        }]
    })

    var textAreaDinamico = new Ext.form.TextArea({
        fieldLabel: 'Dinâmico'
    })

    var form = new Ext.form.FormPanel({
        url: 'teste.php',
        renderTo: Ext.getBody(),
        title: 'Formulário',
        items: [combo, campo, campo2, comboDinamico, campoDinamico,
                cbgDinamico, cbDinamico, dateDinamico, timeDinamico,
                numberDinamico, rgDinamico, textAreaDinamico],
        buttons: [{
            text: 'Salvar',
            handler: function(){
                form.getForm().submit();
            }
        }]
    })

});
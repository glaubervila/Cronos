Ext.onReady(function(){
    Ext.form.FormPanel.prototype.labelAlign = 'right';
    Ext.form.FormPanel.prototype.labelWidth = 60;

    var btMostrarDados = {
                xtype: 'button',
                text: 'Mostrar dados',
                handler: function(){
                    var v = this.findParentByType('form').getForm().getFieldValues();
                    Ext.Msg.alert('Valores', Ext.encode(v));
                }
            }

    new Ext.Panel({
        title: 'Exemplo alternando com/sem mascara',
        renderTo: Ext.getBody(),
        style: 'margin: 10px',
        height: 'auto',
        width: 250,
        frame: true,
        items: [{
            xtype: 'form',
            labelAlign: 'top',
            items: [{
                xtype: 'masktextfield',
                ref: 'campo',
                fieldLabel: 'Sem Mascara',
                mask: '(99) 9999-9999',
                useMask: false,
                value: '0000000000'
            },{
                xtype: 'button',
                text: 'Ativar Mascara',
                ativo: false,
                handler: function(){
                    if(this.ativo){
                        this.findParentByType('form').campo.useMask = false;
                        this.findParentByType('form').campo.setValue(this.findParentByType('form').campo.getValue());
                        this.findParentByType('form').campo.label.update('Sem Mascara:');
                        this.setText('Ativar Mascara');
                        this.ativo = false;
                    }else{
                        this.findParentByType('form').campo.useMask = true;
                        this.findParentByType('form').campo.setValue(this.findParentByType('form').campo.getValue());
                        this.findParentByType('form').campo.label.update('Com Mascara:');
                        this.setText('Desativar Mascara');
                        this.ativo = true;
                    }
                }
            },btMostrarDados]
        }]
    })

    new Ext.Panel({
        title: 'Exemplo mascara dinheiro',
        renderTo: Ext.getBody(),
        style: 'margin: 10px',
        height: 'auto',
        width: 250,
        frame: true,
        items: [{
            xtype: 'form',
            items: [{
                xtype: 'masktextfield',
                fieldLabel: 'Valor',
                mask: 'R$ #9.999.990,00',
                money: true
            },{
                xtype: 'masktextfield',
                fieldLabel: 'Completo',
                mask: '% #0.0',
                money: true
            },btMostrarDados]
        }]
    })

    new Ext.Panel({
        title: 'Exemplo mascara normal',
        renderTo: Ext.getBody(),
        style: 'margin: 10px',
        height: 'auto',
        width: 250,
        frame: true,
        items: [{
            xtype: 'form',
            items: [{
                xtype: 'masktextfield',
                fieldLabel: 'Telefone',
                mask: '(99) 9999-9999',
                money: false
            },{
                xtype: 'masktextfield',
                fieldLabel: 'celular',
                mask: '(99) 9999-9999',
                value: '7796685248',
                money: false
            },{
                xtype: 'masktextfield',
                fieldLabel: 'CPF',
                mask: '999.999.999-99',
                money: false
            },{
                xtype: 'masktextfield',
                fieldLabel: 'Placa',
                mask: 'AAA-9999',
                money: false
            },btMostrarDados]
        }]
    })

    new Ext.Panel({
        title: 'Exemplo mascara data',
        renderTo: Ext.getBody(),
        style: 'margin: 10px',
        height: 'auto',
        width: 250,
        frame: true,
        items: [{
            xtype: 'form',
            items: [{
                xtype: 'maskdatefield',
                fieldLabel: 'Data'
            },btMostrarDados]
        }]
    })

    new Ext.Panel({
        title: 'Trocando mascara',
        renderTo: Ext.getBody(),
        style: 'margin: 10px',
        height: 'auto',
        width: 250,
        frame: true,
        items: [{
            xtype: 'form',
            items: [{
                xtype: 'masktextfield',
                fieldLabel: 'CPF',
                ref: 'cpf',
                mask: '999.999.999-99',
                money: false
            },{
                xtype: 'button',
                text: 'Mudar para CNPJ',
                cpf: true,
                handler: function(){
                    if(this.cpf){
                        this.findParentByType('form').cpf.setMask('99.999.999/9999-99');
                        this.findParentByType('form').cpf.label.update('CNPJ:');
                        this.setText('Mudar para CPF');
                        this.cpf = false;
                    }else{
                        this.findParentByType('form').cpf.setMask('999.999.999-99');
                        this.findParentByType('form').cpf.label.update('CPF:');
                        this.setText('Mudar para CNPJ');
                        this.cpf = true;
                    }
                }
            },btMostrarDados]
        }]
    })

    new Ext.Panel({
        title: 'Exemplo mascara em grid',
        renderTo: Ext.getBody(),
        style: 'margin: 10px',
        height: 'auto',
        width: 250,
        frame: true,
        items: [{
            xtype: 'grid',
            autoHeight: true,
            store: {
                xtype: 'arraystore',
                fields: ['cpf', 'tel'],
                data: [
                    ['96582482514', '9658254155'],
                    ['10000000000', '5196587521'],
                    ['21121564132', '2152485672']
                ]
            },
            columns: [{
                header: 'CPF',
                dataIndex: 'cpf',
                renderer: Ext.util.Format.maskRenderer('999.999.999-99')
            },{
                header: 'Telefone',
                dataIndex: 'tel',
                renderer: Ext.util.Format.maskRenderer('(99) 9999-9999')
            }]
        }]
    })
})

/**
 * Este Override é utilizado na CheckColumm
 */
Ext.override(Ext.grid.ColumnModel, {
	setConfig: Ext.grid.ColumnModel.prototype.setConfig.createSequence(function(config, initial){
		for(var i = 0, len = config.length; i < len; i++){
			var c = config[i];
			if(c.checkbox){
				c.id = c.dataIndex;
				c.align = c.align || 'center';
				if(c.editor){
					c.renderer = this.rendererCheckEditable;
				}else{
					c.renderer = this.rendererCheck;
				}
			}
		}
	}),
	rendererCheck: function(v, p, record){
		p.css += ' x-grid3-check-col-td';
		return '<div class="x-grid3-check-col' + (v ? '-on' : '') + ' x-grid3-col-' + this.id + '" idCol="'+this.id+'" editable="false">&#160;</div>';
	},
	rendererCheckEditable: function(v, p, record){
		p.css += ' x-grid3-check-col-td';
		return '<div class="x-grid3-check-col' + (v ? '-on' : '') + ' x-grid3-col-' + this.id + '" idCol="'+this.id+'" editable="true">&#160;</div>';
	},
	onMouseDown: function(e, t){
		if (t.className && t.className.indexOf('x-grid3-check-col') != -1) {
			e.stopEvent();
			if(t.getAttribute('editable')=='true'){
				var index = this.grid.getView().findRowIndex(t);
				var record = this.grid.store.getAt(index);
				var idCol = t.getAttribute('idcol');
				record.set(idCol, !record.data[idCol]);
				var o = {
					grid: this.grid,
					record: record,
					field: idCol,
					value: record.data[idCol],
					originalValue: !record.data[idCol],
					row: index,
					column: this.grid.getView().findHeaderIndex(t)
				}
				this.grid.fireEvent('afteredit', o);
			}
		}
	}
})

Ext.override(Ext.grid.GridPanel, {
	onRender: Ext.grid.GridPanel.prototype.onRender.createSequence(function(ct, position){
		this.colModel.grid = this;
		if(this.isEditor){
			var view = this.getView();
			view.mainBody.on('mousedown', this.colModel.onMouseDown, this.colModel);
		}
		var view = this.getView();
		var hmenu = view.hmenu;
		if (!view.menuCheck){
			view.sep  = hmenu.addSeparator();
			view.menuCheck = hmenu.add({
				text: 'Marcar Todos',
				itemId: 'selectAll',
				iconCls: 'x-grid3-check-col-on',
				scope: this,
				handler: function(){
					this.getStore().data.each(function(item){
						item.set(view.cm.config[view.hdCtxIndex].dataIndex, true);
					})
				}
			});
			view.menuUnCheck = hmenu.add({
				text: 'Desmarcar Todos',
				itemId: 'unselectAll',
				iconCls: 'x-grid3-check-col',
				scope: this,
				handler: function(){
					this.getStore().data.each(function(item){
						item.set(view.cm.config[view.hdCtxIndex].dataIndex, false);
					})
				}
			});
		}
		hmenu.on('beforeshow', function(){
			var visible = view.cm.config[view.hdCtxIndex].checkbox && view.cm.config[view.hdCtxIndex].editor;
			view.menuCheck.setVisible(visible);
			view.menuUnCheck.setVisible(visible);
			view.sep.setVisible(visible);
		}, this);
	})
})

/**
 * Form Override: Criando colunas facilmente
 * Por
 * Rodrigo K Nascimento
 * Publicado: 25/11/2009
 * Postado em: ExtJS, Overrides
 * http://blog.rkn.com.br/2009/11/form-override-criando-colunas-facilmente-extjs-3-0-x/
 */
Ext.override(Ext.form.FormPanel, {
    vIconSpace: 20,
    colSpace: 5,
    labelWidth: 100,
    ajustFields:function(ff){
        Ext.each(ff.items, function(f, i){
            if((f)&&(f.items)){
                this.ajustFields(f); //MODIFICADO
            }

            var c1 = ff.items[i]; //Campo atual
            var c2 = ff.items[i-1]; //Campo anterior

            if(c1.col&&c2){
                function confField(c){
                    c.labelWidth = Ext.num(c.labelWidth, this.labelWidth); //Largura do Label
                    c.vIconSpace = Ext.num(c.vIconSpace, this.vIconSpace); //Espaço após o campo
                    c.colSpace   = Ext.num(c.colSpace,   this.colSpace); //Espaço antes do campo
                    c.width      = Ext.num(c.width,      100); //Largura padrão MODIFICADO
                    c.msgTarget  = c.msgTarget || this.msgTarget || Ext.form.Field.prototype.msgTarget;
                    c.labelAlign = c.labelAlign || this.labelAlign;
                }

                function calcWidth(w){
                    var x = w.width;
                    x += (w.msgTarget == 'side' ? w.vIconSpace : 0);
                    x += 5;
                    x += (w.labelAlign == 'top' ? 0 : w.labelWidth);
                    return x;
                }

                function createItem(field){
                    return {
                        width: calcWidth.createDelegate(this)(field),
                        border: false,
                        layout: 'form',
                        labelWidth: field.labelWidth,
                        items: field
                    }
                }

                function addColum(c, field){
                    c.items.push(createItem(field))
                }

                if(c2.layout!=='hbox'){
                    confField.createDelegate(this)(c2);
                    c2 = {
                        xtype:'container',//MODIFICADO
                        layout:'hbox',
                        border:false,
                        items:[createItem(c2)]
                    }
                }
                confField.createDelegate(this)(c1);
                c2.items[c2.items.length-1].width += c1.colSpace;
                addColum(c2,c1);
                c1 = c2;
                c2 = 0;

                ff.items[i] = c1;
                ff.items[i-1] = c2;
            }
            delete c1;
            delete c2;
        },this)
        if(Ext.isArray(ff.items)){
            for(i in ff.items){
                ff.items.remove(0);
            }
        }
    },
    createForm:Ext.form.FormPanel.prototype.createForm.createInterceptor(function() {
        this.ajustFields(this);
    })
})


/**
 * Form Override: Asterisco no label de campos obrigatório e desabilitar label
 * Por
 * Fabio Jr. Policeno
 * Publicado: 22/03/2011
 * Postado em: ExtJS, Overrides
 * http:http://www.extjs.com.br/forum/index.php?topic=4758.msg24719#msg24719
 */
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

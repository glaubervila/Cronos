/**
 * autor: Rodrigo Krummenauer do Nascimento
 * site: www.rkn.com.br
 * email: rodrigoknascimento@gmail.com
 *
 * Versão: 3.0
 * Lincença: GPLv3
 **/

/**
 * CHANGE LOG
 *
 * 2.3
 *   - Problema resolvido que ocorrria no Chrome quando se digitava um mesmo numero 3 vezes
 *     na mascara do tipo Money
 *   - Adicionada funcionalidade de poder iniciar o componente com um valor usando a propriedade
 *     value fazendo mascara do valor
 *
 * 2.4
 *   - Resolvido BUG do IE, nenhuma mascara funcionava no IE
 *
 * 2.5
 *   - Problema ao setvar valores sem decimais resolvido
 *
 * 2.6
 *   - Problema de disparar o validade do campo ao apagar tudo selecionando todo o campo e dando um BACKSPACE resolvido.
 *
 * 2.7
 *   - Adicionado suporte a propriedade readOnly
 *
 * 2.8
 *   - Adicionado Ext.util.Format.MoneyMask
 *
 * 2.9
 *   - Adicionada funcionalidade useMask quer ativa ou desativa o uso da classe de mascara.
 *
 * 3.0
 *   - Adicionada funcionalidade de negativar ou positivar o valor da mascara monetária.
 *
 * 3.1
 *   - Corrigido BUG ao usar mascaras com A (letras maiúsculas ou minúsculas)
 **/

 /**
 * TODO
 *
 * - Selection para o IE
 * - Copy e Paste
 *
 **/

/**
 * MODO DE USO DO Ext.ux.TextMask
 *
 * Métodos:
 *    mask(valor) - Mascara um valor
 *    unmask(valor) - Tira a mascara de um valor
 *    setMask(mascara) - Define uma nova mascara para o componente
 *
 *
 * São 2 parametros (mask, money):
 *     mask - Aqui definimos a mascara esta mascara vai ter comportamento diferente dependendo
 *            do valor setado no money.
 *
 *     money - Aqui definimos se teremos uma mascara do tipo dinheiro ou do tipo fixo, true para dinheiro
 *             false para normal.
 *
 * Mascara Normal:
 *    Podemos definir os seguintes caracteres:
 *       _ - Qualquer caracter
 *       A - Letras MAIUSCULAS ou minusculas
 *       L - Letras MAIUSCULAS
 *       l - Letras minusculas
 *       9 - Números de 0 a 9
 *       <!!> - Expressão regular, exemplo <![0123]!> aceita números de 0 a 3
 *
 *    Os demais caracteres serão considerados parte da mascara, exemplo:
 *        var mask = new Ext.ux.TextMask('999.999.999-99', false);
 *        mask.mask('00173008915'); //Deve retornar 001.730.089-15
 *        mask.mask('001'); //Deve retornar 001.___.___-__
 *        mask.unmask('001.730.089-15'); //Deve retornar 00173008915
 *
 *
 * Mascara Money:
 *    Esta mascara funciona basicamente igual a Mascara Normal
 *    Podemos definir todos os caracteres da Mascara Normal e mais o 0 e o #
 *    os zeros serão considerados valor inicial, o # será considerado o fim da mascara
 *    considerando da direita pra esquerda permitindo prefixos e o numero de casas decimais
 *    será contado pela quantidade de caracteres depois do ultimo ponto ou virgula, exemplo:
 *        var mask = new Ext.ux.TextMask('R$ #9.999.990,00', true);
 *        mask.mask(31324587202.18); //Deve retornar R$ 31324.587.202,18
 *        mask.mask(0.01); //Deve retornar R$ 0,01
 *        mask.mask(0); //Deve retornar R$ 0,00
 *        mask.unmask('R$ 31324.587.202,18'); //Deve retornar 31324587202.18
 **/

Ext.ns('Ext.ux.TextMask');

Ext.ux.TextMask = function(mask, money){
    this.money = money === true;
    this.setMask(mask);
}

Ext.ux.TextMask.prototype = {
    blankChar: '_',
    money: false,
    moneyZeros: 0,
    moneyPrecision: 0,
    version: '3.1',
    specialChars: {
        'L': /^[A-Z]$/,
        'l': /^[a-z]$/,
        '9': /^[0-9]$/,
        'A': /^[A-Za-z]$/,
        '_': /^.$/
    },
    mask: function(v){
        return this.money ? this.maskMoney(v) : this.maskNormal(v);
    },
    maskNormal: function(v){
        v = this.unmask(v);
        v = v.split('');
        var m = '';
        var i = 0;
        Ext.each(this.maskList, function(item){
            if(Ext.isString(item)){
                m += item;
            }else{
                if(v[i] && item.test(v[i])){
                    m += v[i];
                }else{
                    m += this.blankChar;
                }
                i++;
            }
        },this)
        return m;
    },
    maskMoney: function(v){
        v = String(this.unmask(v));

        var negativo = false;
        if(v.indexOf('-') >= 0){
            negativo = true;
            v = v.replace(new RegExp('\[-\]', 'g'), '');
        }

        if(Math.round(v) !== v){
            v = Math.round(Number(Ext.num(v,0)) * Number('1'+String.leftPad('', this.moneyPrecision, '0')));
        }
        v = String.leftPad(Number(Ext.num(v,0)), this.moneyZeros, '0');
        v = v.split('');
        var m = '';
        var i = v.length -1;
        var mi = this.maskList.length -1;
        while(i >= 0){
            var item = this.maskList[mi];
            if(mi >= 0){
                if(Ext.isString(item)){
                    m = item + m;
                }else{
                    if(v[i] && item.test(v[i])){
                        m = v[i] + m;
                    }else{
                        m = '0' + m;
                    }
                    i--;
                }
                mi--;
            }else{
                if(this.specialChars['9'].test(v[i])){
                    m = v[i] + m;
                }
                i--;
            }
        }
        if(this.textMask.indexOf('#') >= 0){
            m = this.textMask.slice(0, this.textMask.indexOf('#')) + (negativo ? '-' : '') + m;
        }
        return m;
    },
    unmask: function(v){
        return this.money ? this.unmaskMoney(v) : this.unmaskNormal(v);
    },
    unmaskNormal: function(v){
        v = String(v || "");
        var specialChars = '';
        Ext.iterate(this.specialChars, function(k){
            specialChars += k;
        })
        var chars = this.textMask.replace(new RegExp('\['+specialChars+'\]', 'g'), '');

        v = v.replace(new RegExp('\['+chars+'\]','g'), '');
        v = v.split('');
        var m = '';
        var i = 0;
        Ext.each(this.maskList, function(item){
            if(!Ext.isString(item)){
                if(v[i] && item.test(v[i])){
                    m += v[i];
                }
                i++;
            }
        },this)
        return m;
    },
    unmaskMoney: function(v){
        v = String(v || "");

        if(v.indexOf('+') >= 0){
            v = v.replace(new RegExp('\[-\]', 'g'), '');
        }

        var negativo = v.indexOf('-') >= 0;

        var precision = v.lastIndexOf('.');
        if(precision === -1){
            precision = 0;
        }else{
            precision = v.length - precision - 1;
        }
        if(precision > this.moneyPrecision){
            v = v.slice(0, - (precision - this.moneyPrecision));
            precision = this.moneyPrecision;
        }

        var specialChars = '';
        Ext.iterate(this.specialChars, function(k){
            specialChars += k;
        })
        var chars = this.textMask.replace(new RegExp('\['+specialChars+'\]', 'g'), '');
        v = v.replace(new RegExp('\['+chars+'\]','g'), '');
        v = v.split('');
        var m = '';
        var i = v.length -1;
        var mi = this.maskList.length -1;
        while(i >= 0){
            if(mi >= 0){
                var item = this.maskList[mi];
                if(!Ext.isString(item)){
                    if(v[i] && item.test(v[i])){
                        m = v[i] + m;
                    }
                    i--;
                }
                mi--;
            }else{
                if(v[i] && this.specialChars['9'].test(v[i])){
                    m = v[i] + m;
                }
                i--;
            }
        }

        m = this.parsePrecision(m, precision);

        if(negativo){
            m = '-'+m;
        }

        return String(m);
    },
    parsePrecision: function(v,precision){
        v = String(v);

        var sinal = v.indexOf('-') >= 0 ? '-' : '';

        v = v + String.leftPad('', this.moneyPrecision - precision, '0');
        if(this.moneyPrecision > 0){
            v = String.leftPad(v, this.moneyPrecision+1, '0');
            return sinal + String(Ext.num(v.slice(0, -this.moneyPrecision),0))+'.'+v.slice(-this.moneyPrecision);
        }else{
            return sinal + v;
        }
    },
    parseMask: function(mask){
        var regList = [];

        if(this.money){
            this.moneyZeros = 0;
            while(mask.indexOf('0') >= 0){
                mask = mask.replace('0', '9');
                this.moneyZeros++;
            }
            this.moneyPrecision = Math.min(mask.length - Math.max(mask.lastIndexOf('.'), mask.lastIndexOf(',')) -1, mask.length);
        }
        //
        Ext.each(mask.match(/<![^<][^!]*!>/g), function(exp){
            regList.push(new RegExp('^'+exp.replace(/(<!)|(!>)/g, '')+'$', ''));
        })
        mask = mask.replace(/<![^<][^!]*!>/g, '?');

        this.textMask = mask;
        if(this.money){
            mask = mask.slice(mask.indexOf('#')+1);
        }

        this.maskList = [];
        var regI = 0;
        var maskArr = mask.split('');
        for(var i = 0; i < maskArr.length; i++){
            if(maskArr[i] === '?'){
                this.maskList.push(regList[regI]);
                regI++;
            }else{
                this.maskList.push(this.specialChars[maskArr[i]]||maskArr[i]);
            }
        }
        return this.maskList;
    },
    getLength: function(v){
        v = this.mask(v);
        var i = v.indexOf(this.blankChar);
        if(i === -1){
            i = v.length;
        }
        return i;
    },
    setMask: function(mask){
        if(!Ext.isEmpty(mask) && mask !== this.oldMask){
            this.oldMkask = mask;
            this.parseMask(mask);
        }else if(Ext.isEmpty(this.oldMask)){
            this.parseMask('');
        }
        return this;
    }
}

/**
 * MODO DE USO DO Ext.ux.form.MaskTextField (xtype: 'masktextfield')
 *
 * var campo = new Ext.ux.form.MaskTextField({
 *   mask: '(099) 9999-9999',
 *   money: false
 * })
 *
 * Temos a função setMask(mascara) que serve para mudar a mascara
 * depois que o objeto já estiver criado.
 **/
Ext.ns('Ext.ux.form');
Ext.ux.form.MaskTextField = Ext.extend(Ext.form.TextField,{
    useMask: true,
    initComponent: function(){
        Ext.ux.form.MaskTextField.superclass.initComponent.apply(this, arguments);
        this.textMask = new Ext.ux.TextMask(this.mask, this.money);
    },
    onRender: function(){
        Ext.ux.form.MaskTextField.superclass.onRender.apply(this, arguments);

        if(this.money){
            this.el.setStyle('text-align', 'right');
        }

        this.hiddenField = this.el.insertSibling({
            tag: 'input',
            type: 'hidden',
            name: this.name,
            value: this.textMask.mask(this.value)
        }, 'after');

        this.hiddenName = this.name;
        this.el.dom.removeAttribute('name');
        this.enableKeyEvents = true;
        this.el.on({
            keypress:this.updateHidden,
            keydown:function(e){
                if(this.readOnly){return false};
                if(e.getKey() == e.BACKSPACE){
                    if(this.money){
                        this.hiddenField.dom.value = this.hiddenField.dom.value.substr(0, this.hiddenField.dom.value.length-1);
                        this.hiddenField.dom.value = this.hiddenField.dom.value.replace(/[.]/g, '');
                        this.hiddenField.dom.value = this.textMask.parsePrecision(this.hiddenField.dom.value, this.textMask.moneyPrecision);
                        this.hiddenField.dom.value = this.textMask.unmask(this.hiddenField.dom.value);
                    }else{
                        this.hiddenField.dom.value = this.hiddenField.dom.value.substr(0, this.hiddenField.dom.value.length-1);
                    }
                    this.updateHidden(e);
                }
                this.keyDownEventKey = e.getKey();
            },
            keyup:this.simpleUpdateHidden,
            scope:this
        });
        this.el.dom.value = this.textMask.mask(this.hiddenField.dom.value);
        this.setValue(this.value);
    },
    getKeyCode : function(onKeyDownEvent, type) {
        if(this.readOnly){return false};
        var keycode = {};
        keycode.unicode = onKeyDownEvent.getKey();
        keycode.isShiftPressed = onKeyDownEvent.shiftKey;

        keycode.isDelete = ((onKeyDownEvent.getKey() == Ext.EventObject.DELETE && type=='keydown') || ( type=='keypress' && onKeyDownEvent.charCode===0 && onKeyDownEvent.keyCode == Ext.EventObject.DELETE))? true: false;
        keycode.isTab = (onKeyDownEvent.getKey() == Ext.EventObject.TAB)? true: false;
        keycode.isBackspace = (onKeyDownEvent.getKey() == Ext.EventObject.BACKSPACE)? true: false;
        keycode.isLeftOrRightArrow = (onKeyDownEvent.getKey() == Ext.EventObject.LEFT || onKeyDownEvent.getKey() == Ext.EventObject.RIGHT)? true: false;
        keycode.pressedKey = String.fromCharCode(keycode.unicode);
        return(keycode);
    },
    updateHidden: function(e){
        if(this.readOnly || !this.useMask){return false};
        var key = this.getKeyCode(e, 'keydown');
        var kk = this.keyDownEventKey || e.getKey();

        if(!(kk >= e.F1 && kk <= e.F12) && !e.isNavKeyPress()){
            if(this.el.dom.selectionStart == 0 && this.el.dom.selectionEnd == this.el.dom.value.length){
                this.hiddenField.dom.value = this.money ? 0 : '';
            }
            if(!key.isBackspace){
                if(this.money){
                    this.hiddenField.dom.value = String(this.hiddenField.dom.value) + String(key.pressedKey);
                    this.hiddenField.dom.value = this.hiddenField.dom.value.replace(/[.]/g, '');
                    this.hiddenField.dom.value = this.textMask.parsePrecision(this.hiddenField.dom.value, this.textMask.moneyPrecision);
                    this.hiddenField.dom.value = this.textMask.unmask(this.hiddenField.dom.value);
                }else{
                    this.hiddenField.dom.value = this.textMask.unmask(this.hiddenField.dom.value + key.pressedKey);
                }
            }

            this.el.dom.value = this.textMask.mask(this.hiddenField.dom.value);
            this.el.dom.selectionStart = this.textMask.getLength(this.hiddenField.dom.value);
            this.el.dom.selectionEnd = this.el.dom.selectionStart;

            e.preventDefault();
        }
    },
    simpleUpdateHidden: function(e){
        if(this.readOnly || this.useMask){return false};
        this.hiddenField.dom.value = this.el.dom.value;
    },
    getValue: function(){
        return this.hiddenField.dom.value;
    },
    getRawValue: function(){
        return this.getValue();
    },
    setValue: function(v){
        if(this.useMask)
        {
            if(this.el){
                this.hiddenField.dom.value = this.textMask.unmask(v);
                this.el.dom.value = this.textMask.mask(v);
            }
            this.value = this.textMask.unmask(v);
        }
        else
        {
            if(this.el){
                this.hiddenField.dom.value = v;
                this.el.dom.value = v;
            }
            this.value = v;
        }
    },
    setMask: function(mask){
        this.textMask.setMask(mask);
        this.setValue(this.hiddenField.dom.value);
    }
})
Ext.reg('masktextfield', Ext.ux.form.MaskTextField);

/**
 * MODO DE USO DO Ext.ux.form.MaskDateField (xtype: 'maskdatefield')
 *
 * var campo = new Ext.ux.form.MaskTextField({
 *   //Não precisamos definir nada, o componente mascara de acordo com o formato
 *   //da data que já está definido no componente.
 * })
 **/
Ext.form.DateField.prototype.altFormats = 'd|dm|dmY|d/m|d-m|d/m/Y|d-m-Y|Y-m-d|Y-m-dTg:i:s';
Ext.ux.form.MaskDateField = Ext.extend(Ext.form.DateField,{
    maskRel: {
        m: '99',
        d: '99',
        n: '99',
        j: '99',
        Y: '9999'
    },
    initComponent: function(){
        this.mask = '';
        Ext.each(this.format.split(''), function(item){
            this.mask += this.maskRel[item] || item
        },this)

        Ext.ux.form.MaskDateField.superclass.initComponent.apply(this, arguments);
        this.textMask = new Ext.ux.TextMask(this.mask);
        this.textMask.blankChar = '_';
    },
    onRender: function(){
        Ext.ux.form.MaskDateField.superclass.onRender.apply(this, arguments);
        this.hiddenField = this.el.insertSibling({
            tag: 'input',
            type: 'hidden',
            name: this.name,
            value: this.textMask.unmask(this.value)
        }, 'after');
        this.hiddenName = this.name;
        this.el.dom.removeAttribute('name');
        this.enableKeyEvents = true;
        this.el.on({
            keypress:this.updateHidden,
            keydown: function(e){
                if(this.readOnly){return false};
                if(e.getKey() == e.BACKSPACE){
                    this.hiddenField.dom.value = this.hiddenField.dom.value.substr(0, this.hiddenField.dom.value.length-1);
                    this.updateHidden(e);
                }
            },
            scope:this
        });
        this.setValue(this.value);
    },
    getKeyCode : function(onKeyDownEvent, type) {
        if(this.readOnly){return false};
        var keycode = {};
        keycode.unicode = onKeyDownEvent.getKey();
        keycode.isShiftPressed = onKeyDownEvent.shiftKey;

        keycode.isDelete = ((onKeyDownEvent.getKey() == Ext.EventObject.DELETE && type=='keydown') || ( type=='keypress' && onKeyDownEvent.charCode===0 && onKeyDownEvent.keyCode == Ext.EventObject.DELETE))? true: false;
        keycode.isTab = (onKeyDownEvent.getKey() == Ext.EventObject.TAB)? true: false;
        keycode.isBackspace = (onKeyDownEvent.getKey() == Ext.EventObject.BACKSPACE)? true: false;
        keycode.isLeftOrRightArrow = (onKeyDownEvent.getKey() == Ext.EventObject.LEFT || onKeyDownEvent.getKey() == Ext.EventObject.RIGHT)? true: false;
        keycode.pressedKey = String.fromCharCode(keycode.unicode);
        return(keycode);
    },
    updateHidden: function(e){
        if(this.readOnly){return false};
        var key = this.getKeyCode(e, 'keydown');
        if(!(e.getKey() >= e.F1 && e.getKey() <= e.F12) && !e.isNavKeyPress()){
            if(this.el.dom.selectionStart == 0 && this.el.dom.selectionEnd == this.el.dom.value.length){
                this.hiddenField.dom.value = '';
            }
            if(!key.isBackspace){
                this.hiddenField.dom.value = this.textMask.unmask(this.hiddenField.dom.value + key.pressedKey);
            }

            this.el.dom.value = this.textMask.mask(this.hiddenField.dom.value);
            this.el.dom.selectionStart = this.textMask.getLength(this.hiddenField.dom.value);
            this.el.dom.selectionEnd = this.el.dom.selectionStart;

            e.preventDefault();
        }
    },
    getRawValue: function(){
        return this.hiddenField.dom.value;
    },
    setValue: function(v){
        if(v === 'now'){
            v = new Date;
        }

        if(this.el){
            v = this.formatDate(this.parseDate(v));
            this.hiddenField.dom.value = v;
            this.el.dom.value = this.textMask.mask(v);
        }
        this.value = v;
    },
    //Correção de bug, só dava parse na mascara se fosse um TAB
    onFocus: function(){
        Ext.form.TriggerField.superclass.onFocus.call(this);
        if(!this.mimicing){
            this.wrap.addClass(this.wrapFocusClass);
            this.mimicing = true;
            this.doc.on('mousedown', this.mimicBlur, this, {delay: 10});
            if(this.monitorTab){
                this.on('keydown', this.checkTab, this);
            }
        }
    },
    checkTab: function(me, e){
        if(e.getKey() == e.TAB || e.getKey() == e.ENTER){
            this.triggerBlur();
        }
    }
})
Ext.reg('maskdatefield', Ext.ux.form.MaskDateField);

/**
 * ATENÇÃO
 * Usado pra substituir todos os datefields que uso pelo datefield com mascara
 * isto ocorre sobreescrevendo o xtype do datefield normal, faço isso pq uso
 * sómente xtypes no meu sistema, caso não queira isto apague a linha abaixo.
 **/
Ext.reg('datefield', Ext.ux.form.MaskDateField);

/**
 * Aqui temos um Sigleton para a mascara, use para formatações rapidas, exemplo
 *   Ext.util.Format.TextMask.setMask('99/99/9999').mask('10102010'); //Vai retornar 10/10/2010
 **/
Ext.util.Format.TextMask = new Ext.ux.TextMask();

/**
 * Aqui temos um Sigleton para a mascara de valores, use para formatações rapidas, exemplo
 *   Ext.util.Format.MoneyMask.setMask('R$#9.990,00').mask('100'); //Vai retornar R$100,00
 **/
Ext.util.Format.MoneyMask = new Ext.ux.TextMask('', true);

/**
 * Aqui temos um renderer pra colunas de um grid
 *   {
 *     header: 'Telefone',
 *     dataIndex: 'fone',
 *     renderer: Ext.util.Format.maskRenderer('(099) 9999-9999')
 *   }
 **/
Ext.util.Format.maskRenderer = function(mask, money){
    return function(v){
        Ext.util.Format.TextMask.money = money;
        return Ext.util.Format.TextMask.setMask(mask).mask(v);
    }
}

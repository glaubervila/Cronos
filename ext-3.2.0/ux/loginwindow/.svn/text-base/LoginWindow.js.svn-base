/**
 * Tutorial ensinando a criar uma extens�o de tela de autentica��o
 * Desenvolvido por Bruno Tavares
 * Publicado em http://www.extdesenv.com.br
 */

Ext.ns('Ext.ux');

/**
 * @class Ext.ux.LoginWindow
 * @extends Ext.Window
 * <p>Classe que representa uma tela de login com configura��es pr�-definidas. � poss�vel configur�-la com apenas uma linha  de c�digo ou ent�o utilizar configura��es personalizadas. Examplos de uso:</p>
 * <pre><code>//Config 1 - informando somente a URL de valida��o
new Ext.ux.LoginWindow({
   url: 'login.ajax.php'
}).show();

//Config 2 - Personalizando os campos de texto e informando url de redirecionamento
new Ext.ux.LoginWindow({
   url         : 'login.ajax.php'
   ,redirectUrl: 'Principal.php'
   ,labelWidth : 100
   ,loginField : {
      fieldLabel: 'Username'
      ,emptyText: 'type your username'
   }
   ,senhaField: {
      fieldLabel: 'Password'
      ,emptyText: ''
   }
}).show();</code></pre><p>Este plugin suporta algumas configura��es feitas no servidor. Se a autentica��o foi realizada com sucesso
� OBRIGAT�RIO que a resposta seja um JSON com a propriedade <i>success:true</i>, e quando n�o foi realizada com sucesso a resposta deve ter <i>success:false</i>. Em
complemento voc� pode informar pelo servidor a URL que o usu�rio ser� redirecionado, ou at� mesmo uma mensagem de erro personalizada:</p><pre><code>
if( $isLoggedIn )
{
   echo json_encode(array(
      success	=> true
      ,redirect	=> "principal.php"
   ));
}
else
{
   echo json_encode(array(
      success=> false
      ,message=>utf8_encode("Ops! Algo n�o est� certo...")
   ));
}
</code></pre><p>Obrigat�rio: informar {@link Ext.ux.LoginWindow#url URL}, e que a resposta do servidor tenha success:true. O restante caso n�o informado ir� assumir valores padr�es.</p>*/
Ext.ux.LoginWindow = Ext.extend(Ext.Window,{

	//Config Options {

		/**@cfg {String} url URL que ser� utilizada para lan�ar a requisi��o de autentica��o do usu�rio. A requisi��o utiliza m�todo POST e envia os par�metros login e senha.
		/**@cfg {Object} params Parametros adicionais a serem passados para a requisi��o de autentica��o*/
		/**@cfg {String} redirectUrl Caso a autentica��o ocorra com sucesso, o usu�rio ser� redirecionado para esta URL. Tamb�m � poss�vel informar pelo servidor qual o destino da autentica��o. Veja mais no exemplo dessa extenss�o*/
		/**@cfg {String/Object} loginField Uma string que representa a label do campo de login, ou ent�o um objeto de configura��o aceito por Ext.form.TextField*/
		/**@cfg {String/Object} senhaField Uma string que representa a label do campo de senha, ou ent�o um objeto de configura��o aceito por Ext.form.TextField*/

		 iconCls	: 'ico-cadeado'
		,layout		: 'form'
		,bodyStyle	: 'padding:10px;'
		,title		: 'Autentica&#231;&#227;o'
		,labelAlign	: 'right'
		,closable	: false
		,constrain	: true
		,width		: 300
		,height		: 140
		,labelWidth	: 45
		,minHeight	: 140
		,minWidth	: 220

	//}

	//Inits {

		,initComponent: function()
		{
			if(Ext.isString(this.loginField))
				this.loginField = {fieldLabel: this.loginField};

			if(Ext.isString(this.senhaField))
				this.senhaField = {fieldLabel: this.senhaField};

			Ext.apply(this,{
				defaults:{
					anchor: '-18'
				}
				,items	: [
					Ext.apply({
						 xtype			: 'textfield'
						,fieldLabel		: 'Login'
						,emptyText		: 'Informe seu login'
						,msgTarget		: 'side'
						,itemId			: 'txtLogin'
						,allowBlank		: false
						,selectOnFocus	: true
						,enableKeyEvents: true
						,listeners		: {
							 scope	: this
							,'keyup': this._onTxtKeyUp
						}
					},this.loginField)
					,
					Ext.apply({
						 xtype			: 'textfield'
						,inputType		: 'password'
						,fieldLabel		: 'Senha'
						,emptyText		: '*fakepass*'
						,msgTarget		: 'side'
						,itemId			: 'txtSenha'
						,allowBlank		: false
						,selectOnFocus	: true
						,enableKeyEvents: true
						,listeners		: {
							 scope	: this
							,'keyup': this._onTxtKeyUp
						}
					},this.senhaField)
				]
				,buttons: [{
					 xtype	: 'button'
					,text	: 'Entrar'
					,iconCls: 'ico-app-go'
					,scope	: this
					,handler: this._onBtnEntrarClick
				}]
			});

			Ext.ux.LoginWindow.superclass.initComponent.call(this);
		}

	//}

	//Overrides {

/*		,onRender: function()
		{
            		Ext.ux.LoginWindow.superclass.onRender.apply(this, arguments);

			var btnCt = this.footer.child('.x-panel-btns');
			this._errorCt = btnCt.insertFirst({
				 cls: 'error-msg'
				,cn	: ''
			});
		}*/

	//}

	//Listeners{

		,_onTxtKeyUp: function(txt,e)
		{
			if(e.getKey() === e.ENTER)
			{
				e.stopEvent();
				this._onBtnEntrarClick();
			}
		}

		,_onBtnEntrarClick: function()
		{
			var txtLogin = this.getComponent('txtLogin');
			var txtSenha = this.getComponent('txtSenha');

			if(!txtLogin.isValid() && !txtSenha.isValid())
				return false;

			this.buttons[0].disable();

			Ext.Ajax.request({
				 url	: this.url
				,method	: 'POST'
				,scope	: this
				,params	: Ext.applyIf({
					login: txtLogin.getValue()
					, senha: txtSenha.getValue()
				},this.params)
				,success: function(response)
				{
					response = Ext.decode(response.responseText);

					if( response.success )
					{
						this.el.mask();
						window.location.href = response.redirect||this.redirectUrl;
					}
					else
					{
						//this._errorCt.update(response.message||"Login e/ou senha inv&#225;lidos");
						//this._errorCt.fadeIn();
                        Ext.Msg.alert('Status', 'Falha na autenticação!<br>Descricão: ' + response.message);
						txtLogin.focus();
					}
				}
				,callback: function()
				{
					this.buttons[0].enable();
				}
			});
		}

	//}


});

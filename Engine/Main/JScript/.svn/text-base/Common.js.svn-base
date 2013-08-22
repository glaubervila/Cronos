/**
 * Json de Permissoes
 * Neste Json ficam armazenadas todas as permissões do usuário
 */

var permissoes_store = new Ext.data.JsonStore({
    id               : 'permissoes_store'
    , url            : 'main.php'
    , root           : 'rows'
    , idProperty     : 'id'
    , autoLoad       : true
    , autoDestroy    : true
    , baseParams     : {
        classe  : 'Permissoes'
        , action : 'retorna_todas_permissoes'
    }
    , fields:[
        {name  : 'id'     , type:'int'}
        , {name: 'Usuario', type:'int'}
        , {name: 'Tela'   , type:'int'}
        , {name: 'identificacao', type:'string'}
        , {name: 'ins'    , type:'int'}
        , {name: 'upd'    , type:'int'}
        , {name: 'del'    , type:'int'}
        , {name: 'imp'    , type:'int'}
        , {name: 'exc'    , type:'int'}
    ]
});



// Combo de Estados / UF
var Cmb_Uf = Ext.extend(Ext.form.ComboBox,{

    root             : 'rows'
    //, id             : 'Cmb_Uf'
    , fieldLabel     : 'UF'
    , hiddenName     : 'Uf'
    , triggerAction  : 'all'
    , forceSelection : true
    , width          : 80
    , mode           : 'local'
    , valueField     : 'UF'
    , displayField   : 'UF'
    , emptyText      : 'Selecione'
    , store: new Ext.data.SimpleStore({
        fields:['id', 'UF', 'Nome']
        , data: [
            [1, 'AC', 'Acre']
            , [2, 'AL', 'Alagoas']
            , [3, 'AP', 'Amapá']
            , [4, 'AM', 'Amazonas']
            , [5, 'BA', 'Bahia']
            , [6, 'CE', 'Ceará']
            , [7, 'DF', 'Distrito Federal']
            , [8, 'ES', 'Espirito Santo']
            , [9, 'GO', 'Goiás']
            , [10, 'MA', 'Maranhão']
            , [11, 'MT', 'Mato Grosso']
            , [12, 'MS', 'Mato Grosso do Sul']
            , [13, 'MG', 'Minas Gerais']
            , [14, 'PA', 'Pará']
            , [15, 'PB', 'Paraíba']
            , [16, 'PR', 'Paraná']
            , [17, 'PE', 'Pernambuco']
            , [18, 'PI', 'Piauí']
            , [19, 'RN', 'Rio Grande do Norte']
            , [20, 'RS', 'Rio Grande do Sul']
            , [21, 'RJ', 'Rio de Janeiro']
            , [22, 'RO', 'Rondônia']
            , [23, 'RR', 'Roraima']
            , [24, 'SC', 'Santa Catarina']
            , [25, 'SP', 'São Paulo']
            , [26, 'SE', 'Sergipe']
            , [27, 'TO', 'Tocantins']
        ]
    })
});
Ext.reg('e-Cmb_Uf', Cmb_Uf);


// Combo Tipo de Endereços
var Cmb_Tp_Endereco = Ext.extend(Ext.form.ComboBox,{
    root             : 'rows'
    , fieldLabel     : 'Tipo de Endere&ccedil;o'
    , hiddenName     : 'Tp_Endereco'
    , triggerAction  : 'all'
    , forceSelection : true
    , width          : 100
    , mode           : 'local'
    , valueField     : 'Valor'
    , displayField   : 'Label'
    , emptyText      : 'Selecione'
    , store: new Ext.data.SimpleStore({
        fields:['id', 'Valor', 'Label']
        , data: [
            [1, '1', 'Residencial']
            , [2, '2', 'Comercial']
        ]
    })
});
Ext.reg('e-Cmb_Tp_Endereco', Cmb_Tp_Endereco);


// Combo Tipo de Pessoa
var Cmb_Tp_Pessoa = Ext.extend(Ext.form.ComboBox,{
    root             : 'rows'
    , fieldLabel     : 'Tipo'
    , hiddenName     : 'Tp_Pessoa'
    , triggerAction  : 'all'
    , forceSelection : true
    , width          : 120
    , mode           : 'local'
    , valueField     : 'Valor'
    , displayField   : 'Label'
    , emptyText      : 'Selecione'
    , store: new Ext.data.SimpleStore({
        fields:['id', 'Valor', 'Label']
        , data: [
            [1, '1', 'Pessoa Física']
            , [2, '2', 'Pessoa Jurídica']
        ]
    })
});
Ext.reg('e-Cmb_Tp_Pessoa', Cmb_Tp_Pessoa);

// Combo Sexo
var Cmb_Sexo = Ext.extend(Ext.form.ComboBox,{
    root             : 'rows'
    , fieldLabel     : 'Sexo'
    , hiddenName     : 'Sexo'
    , triggerAction  : 'all'
    , forceSelection : true
    , width          : 120
    , mode           : 'local'
    , valueField     : 'Valor'
    , displayField   : 'Label'
    , emptyText      : 'Selecione'
    , store: new Ext.data.SimpleStore({
        fields:['id', 'Valor', 'Label']
        , data: [
            [1, 'M', 'Masculino']
            , [2, 'F', 'Feminino']
        ]
    })
});
Ext.reg('e-Cmb_Sexo', Cmb_Sexo);

// Combo Estado Civil
var Cmb_Estado_Civil = Ext.extend(Ext.form.ComboBox,{
    root             : 'rows'
    , fieldLabel     : 'Estado Civil'
    , hiddenName     : 'Estado_Civil'
    , triggerAction  : 'all'
    , forceSelection : true
    , width          : 120
    , mode           : 'local'
    , valueField     : 'Valor'
    , displayField   : 'Label'
    , emptyText      : 'Selecione'
    , store: new Ext.data.SimpleStore({
        fields:['id', 'Valor', 'Label']
        , data: [
            [1, 1, 'Solteiro(a)']
            , [2, 2, 'Casado(a)']
            , [3, 3, 'Viuvo(a)']
            , [5, 5, 'Divorciado(a)']
            , [6, 6, 'Outros']
        ]
    })
});
Ext.reg('e-Cmb_Estado_Civil', Cmb_Estado_Civil);


// Combo de Unidades
var Cmb_Unidade = Ext.extend(Ext.form.ComboBox,{

    root             : 'rows'
    , fieldLabel     : 'Unidade'
    , hiddenName     : 'cmb_unidade'
    , triggerAction  : 'all'
    , forceSelection : true
    , width          : 80
    , mode           : 'local'
    , valueField     : 'abreviado'
    , displayField   : 'abreviado'
    , emptyText      : 'Selecione'
    , store: new Ext.data.SimpleStore({
        fields:['id', 'abreviado', 'Nome']
        , data: [
            [1, 'Un', 'Unidade']
            , [2, 'Cx', 'Caixa']
            , [3, 'Kg', 'Kilograma']
            , [4, 'Lt', 'Litro']
            , [5, 'Mt', 'Metro']
            , [6, 'Dz', 'Duzia']
            , [7, 'Pc', 'Pacote']
        ]
    })
});
Ext.reg('e-Cmb_Unidade', Cmb_Unidade);



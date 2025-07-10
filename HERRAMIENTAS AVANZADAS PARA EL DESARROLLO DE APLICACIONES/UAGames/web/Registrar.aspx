<%@ Page Title="" Language="C#" UnobtrusiveValidationMode="None" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Registrar.aspx.cs" Inherits="web.Registrar" %>

<%@ Register Assembly="AjaxControlToolkit" Namespace="AjaxControlToolkit" TagPrefix="ajaxToolkit" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height:599px;">
        <div class="registro_inicio">
            <asp:ScriptManager ID="ScriptManager1" runat="server"></asp:ScriptManager>
            <h1>Registrar nuevo usuario</h1>
            Nick:&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TNick" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredNick" ControlToValidate="TNick" runat="server" ErrorMessage="* Introduce el Nick"></asp:RequiredFieldValidator>
            <br />
            Nombre:&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TNombre" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredNombre" ControlToValidate="TNombre" runat="server" ErrorMessage="* Introduce el Nombre"></asp:RequiredFieldValidator>
            <br />
            Apellidos:&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TApellidos" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredApellidos" ControlToValidate="TApellidos" runat="server" ErrorMessage="* Introduce los Apellidos"></asp:RequiredFieldValidator>
            <br />
            Email:&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TEmail" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredEmail" ControlToValidate="TEmail" runat="server" ErrorMessage="* Introduce el Email"></asp:RequiredFieldValidator>
            <asp:RegularExpressionValidator ID="RegularEmail" ControlToValidate="TEmail" runat="server" ErrorMessage="* Email incorrecto" ValidationExpression="\S+@\S+\.\S+"></asp:RegularExpressionValidator>
            <br />
            Fecha de nacimiento (yyyy-MM-dd):&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TFecha" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredFecha" ControlToValidate="TFecha" runat="server" ErrorMessage="* Introduce la Fecha de Nacimiento"></asp:RequiredFieldValidator>
            <asp:CompareValidator ID="CompareFecha" runat="server" ControlToValidate="TFecha" ErrorMessage="* Formato de fecha incorrecto" Operator="DataTypeCheck" Type="Date" ValidationGroup="grpDate" />
            <br />
            Teléfono:&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TTelefono" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredTelefono" ControlToValidate="TTelefono" runat="server" ErrorMessage="* Introduce el Teléfono"></asp:RequiredFieldValidator>
            <asp:RegularExpressionValidator ID="RegularTelefono" ControlToValidate="TTelefono" runat="server" ErrorMessage="* Teléfono incorrecto" ValidationExpression="[0-9]{9}"></asp:RegularExpressionValidator>
            <br />
            <br />
            <br />
            Password:&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TPassword" runat="server" CausesValidation="True" type="password"></asp:TextBox>
            <ajaxToolkit:PasswordStrength ID="PasswordStrength1" runat="server" Enabled="true" TargetControlID="TPassword" DisplayPosition="RightSide" StrengthIndicatorType="Text" 
                PreferredPasswordLength="10" PrefixText="Seguridad: " TextStrengthDescriptions="Débil; Media; Fuerte; Muy fuerte" MinimumSymbolCharacters="1" MinimumLowerCaseCharacters="2" MinimumUpperCaseCharacters="2" MinimumNumericCharacters="2"/>
            <asp:RequiredFieldValidator ID="RequiredPassword" ControlToValidate="TPassword" runat="server" ErrorMessage="* Introduce la Password"></asp:RequiredFieldValidator>
            <br />
            Repita Password:&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TRepitePassword" runat="server" CausesValidation="True" type="password"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredRepite" ControlToValidate="TRepitePassword" runat="server" ErrorMessage="* Repite la Password"></asp:RequiredFieldValidator>
            <br />
            <br />
            <br />
            <asp:Button ID="BCrear" CssClass="btn btn-primary" runat="server" Text="Crear" OnClick="Crear"/>
            <br />
            <asp:Label ID="LResultado" runat="server" Text =" " />
            <br />
        </div>
    </div>
</asp:Content>

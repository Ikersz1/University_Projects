<%@ Page Title="" Language="C#" UnobtrusiveValidationMode="None" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="AdminUsuarios.aspx.cs" Inherits="web.AdminUsuarios" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height:599px;">
        <div class="showGrid">
            <h1>Página de Usuarios</h1>
            Id&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TId" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredId" Enabled="false" ControlToValidate="TId" runat="server" ErrorMessage="* Is obligatoria"></asp:RequiredFieldValidator>
            <br />
            Nick&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TNick" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredNick" Enabled="false" ControlToValidate="TNick" runat="server" ErrorMessage="* Nick obligatorio"></asp:RequiredFieldValidator>
            <br />
            Nombre&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TNombre" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredNombre" Enabled="false" ControlToValidate="TNombre" runat="server" ErrorMessage="* Nombre sin rellenar"></asp:RequiredFieldValidator>
            <br />
            Apellidos&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TApellidos" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredApellidos" Enabled="false" ControlToValidate="TApellidos" runat="server" ErrorMessage="* Apellidos sin rellenar"></asp:RequiredFieldValidator>
            <br />
            Email&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TEmail" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredEmail" Enabled="false" ControlToValidate="TEmail" runat="server" ErrorMessage="* Email sin rellenar"></asp:RequiredFieldValidator>
            <asp:RegularExpressionValidator ID="RegularEmail" Enabled="false" ControlToValidate="TEmail" runat="server" ErrorMessage="* Email incorrecto" ValidationExpression="\S+@\S+\.\S+"></asp:RegularExpressionValidator>
            <br />
            Teléfono&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TTelefono" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredTelefono" Enabled="false" ControlToValidate="TTelefono" runat="server" ErrorMessage="* Teléfono sin rellenar"></asp:RequiredFieldValidator>
            <asp:RegularExpressionValidator ID="RegularTelefono" Enabled="false" ControlToValidate="TTelefono" runat="server" ErrorMessage="* Teléfono incorrecto" ValidationExpression="[0-9]{10}"></asp:RegularExpressionValidator>
            <br />
            Fecha de nacimiento (yyyy-MM-dd)&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TFecha" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredFecha" Enabled="false" ControlToValidate="TFecha" runat="server" ErrorMessage="* Fecha sin rellenar"></asp:RequiredFieldValidator>
            <asp:CompareValidator ID="CompareFecha" runat="server" Enabled="false" ControlToValidate="TFecha" ErrorMessage="* Formato de fecha incorrecto" Operator="DataTypeCheck" Type="Date" ValidationGroup="grpDate" />
            <br />
            Administrador? (true/false)&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TRol" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredRol" Enabled="false" ControlToValidate="TRol" runat="server" ErrorMessage="* Rol sin rellenar"></asp:RequiredFieldValidator>
            <br />
            Password&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TPassword" runat="server" CausesValidation="True"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredPassword" Enabled="false" ControlToValidate="TPassword" runat="server" ErrorMessage="* Password sin rellenar"></asp:RequiredFieldValidator>
            <br />
            <br />
            <asp:Button ID="BLeer_F" runat="server" Text="Leer" OnClick="Leer"/>
            <asp:Button ID="BLeerP_F" runat="server" Text="Leer Primero" OnClick="LeerPrimero"/>
            <asp:Button ID="BLeerA_F" runat="server" Text="Leer Anterior" OnClick="LeerAnterior"/>
            <asp:Button ID="BLeerS_F" runat="server" Text="Leer Siguiente" OnClick="LeerSiguiente"/>
            <asp:Button ID="BCrear_F" runat="server" Text="Crear" OnClick="Crear"/>
            <asp:Button ID="BActualizar_F" runat="server" Text="Actualizar" OnClick="Actualizar"/>
            <asp:Button ID="BBorrar_F" runat="server" Text="Borrar" OnClick="Borrar"/>
            <asp:Label ID="LResultado" runat="server" Text =" " />&nbsp;
            <br />
            <br />
            <h2>Listado de usuarios</h2>
            <div class="table-responsive">
                <asp:GridView ID="GridView1" runat="server" CssClass="grid" AutoGenerateSelectButton="True" selectedindex="1" onselectedindexchanged="Gridview1_SelectedItemChanged" HorizontalAlign="Center" CellPadding="5" PageSize="5" AllowPaging="True" OnPageIndexChanging="ChangePage">
                    <FooterStyle BackColor="White" ForeColor="#0d6efd" />
                    <HeaderStyle BackColor="#0d6efd" Font-Bold="True" ForeColor="White" HorizontalAlign="Center" Font-Underline="false" />
                    <PagerStyle BackColor="White" ForeColor="#0d6efd" HorizontalAlign="Center" />
                    <RowStyle ForeColor="#0d6efd" />
                    <SelectedRowStyle BackColor="#669999" Font-Bold="True" ForeColor="White" />
                </asp:GridView>
            </div>
        </div>
    </div>
</asp:Content>


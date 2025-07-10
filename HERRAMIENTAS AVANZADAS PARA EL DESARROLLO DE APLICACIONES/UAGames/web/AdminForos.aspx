<%@ Page Title="" Language="C#" UnobtrusiveValidationMode="None" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="AdminForos.aspx.cs" Inherits="web.AdminForos" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height:599px;">
            <h1>Página de foros</h1>
            <h2>Leer o modificar lista de foros</h2>
            Id&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TId_F" runat="server" CausesValidation="True"></asp:TextBox>
            <br />
            Nombre&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TNombre" runat="server" CausesValidation="True"></asp:TextBox>
            <br />
            <br />
            <asp:Button ID="BLeer_F" runat="server" Text="Leer" OnClick="Leer" CssClass="btn btn-primary" />
            <asp:Button ID="BLeerP_F" runat="server" Text="Leer Primero" OnClick="LeerPrimero" CssClass="btn btn-primary" />
            <asp:Button ID="BLeerA_F" runat="server" Text="Leer Anterior" OnClick="LeerAnterior"  CssClass="btn btn-primary"/>
            <asp:Button ID="BLeerS_F" runat="server" Text="Leer Siguiente" OnClick="LeerSiguiente" CssClass="btn btn-primary"/>
            <asp:Button ID="BCrear_F" runat="server" Text="Crear" OnClick="Crear" CssClass="btn btn-primary"/>
            <asp:Button ID="BActualizar_F" runat="server" Text="Actualizar" OnClick="Actualizar" CssClass="btn btn-primary"/>
            <asp:Button ID="BBorrar_F" runat="server" Text="Borrar" OnClick="Borrar" CssClass="btn btn-primary"/>
            <br />
            <asp:Label ID="LResultado_F" runat="server" Text=" " />&nbsp;
            <asp:RequiredFieldValidator ID="RequiredForoId" Enabled="false" ControlToValidate="TId_F" runat="server" ErrorMessage="* Id obligatoria"></asp:RequiredFieldValidator>
            <asp:RequiredFieldValidator ID="RequiredForoNombre" Enabled="false" ControlToValidate="TNombre" runat="server" ErrorMessage="* Nombre obligatorio"></asp:RequiredFieldValidator>
            <h2>Leer o modificar lista de temas</h2>
            Id&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TId_T" runat="server" CausesValidation="True"></asp:TextBox>
            <br />
            Título&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TTitulo" runat="server" CausesValidation="True"></asp:TextBox>
            <br />
            <br />
            <asp:Button ID="BLeer_T" runat="server" Text="Leer" OnClick="LeerT" CssClass="btn btn-primary"/>
            <asp:Button ID="BLeerP_T" runat="server" Text="Leer Primero" OnClick="LeerPrimeroT" CssClass="btn btn-primary"/>
            <asp:Button ID="BLeerA_T" runat="server" Text="Leer Anterior" OnClick="LeerAnteriorT" CssClass="btn btn-primary"/>
            <asp:Button ID="BLeerS_T" runat="server" Text="Leer Siguiente" OnClick="LeerSiguienteT" CssClass="btn btn-primary"/>
            <asp:Button ID="BCrear_T" runat="server" Text="Crear" OnClick="CrearT" CssClass="btn btn-primary"/>
            <asp:Button ID="BActualizar_T" runat="server" Text="Actualizar" OnClick="ActualizarT" CssClass="btn btn-primary"/>
            <asp:Button ID="BBorrar_T" runat="server" Text="Borrar" OnClick="BorrarT" CssClass="btn btn-primary"/>
            <br />
            <asp:Label ID="LResultado_T" runat="server" Text=" " />&nbsp;
            <asp:RequiredFieldValidator ID="RequiredTemaId" Enabled="false" ControlToValidate="TId_T" runat="server" ErrorMessage="* Id obligatoria"></asp:RequiredFieldValidator>
            <asp:RequiredFieldValidator ID="RequiredTemaTitulo" Enabled="false" ControlToValidate="TTitulo" runat="server" ErrorMessage="* Título obligatorio"></asp:RequiredFieldValidator>
            <h2>Leer o modificar lista de publicaciones</h2>
            Id&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TId_P" runat="server" CausesValidation="True"></asp:TextBox>
            <br />
            Texto&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TTexto" runat="server" CausesValidation="True"></asp:TextBox>
            <br />
            Id de usuario&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:TextBox ID="TUsuario" runat="server" CausesValidation="True"></asp:TextBox>
            <br />
            <br />
            <asp:Button ID="BLeer_P" runat="server" Text="Leer" OnClick="LeerP" CssClass="btn btn-primary"/>
            <asp:Button ID="BLeerP_P" runat="server" Text="Leer Primero" OnClick="LeerPrimeroP" CssClass="btn btn-primary"/>
            <asp:Button ID="BLeerA_P" runat="server" Text="Leer Anterior" OnClick="LeerAnteriorP" CssClass="btn btn-primary"/>
            <asp:Button ID="BLeerS_P" runat="server" Text="Leer Siguiente" OnClick="LeerSiguienteP" CssClass="btn btn-primary"/>
            <asp:Button ID="BCrear_P" runat="server" Text="Crear" OnClick="CrearP" CssClass="btn btn-primary"/>
            <asp:Button ID="BActualizar_P" runat="server" Text="Actualizar" OnClick="ActualizarP" CssClass="btn btn-primary"/>
            <asp:Button ID="BBorrar_P" runat="server" Text="Borrar" OnClick="BorrarP" CssClass="btn btn-primary"/>
            <br />
            <asp:Label ID="LResultado_P" runat="server" Text=" " />&nbsp;
            <asp:RequiredFieldValidator ID="RequiredPubliId" Enabled="false" ControlToValidate="TId_P" runat="server" ErrorMessage="* Id obligatoria"></asp:RequiredFieldValidator>
            <asp:RequiredFieldValidator ID="RequiredPubliTexto" Enabled="false" ControlToValidate="TTexto" runat="server" ErrorMessage="* Texto obligatorio"></asp:RequiredFieldValidator>
            <asp:RequiredFieldValidator ID="RequiredUsuario" Enabled="false" ControlToValidate="TUsuario" runat="server" ErrorMessage="* Id de usuario obligatoria"></asp:RequiredFieldValidator>
            <h2>Listado de publicaciones</h2>
            <div class="table-responsive">
                <asp:GridView ID="GridView1" runat="server" CssClass="grid" AutoGenerateSelectButton="true" selectedindex="1" onselectedindexchanged="Gridview1_SelectedItemChanged" CellPadding="5" PageSize="5" AllowPaging="True" OnPageIndexChanging="ChangePage">
                    <FooterStyle BackColor="White" ForeColor="#0d6efd" />
                    <HeaderStyle BackColor="#0d6efd" Font-Bold="True" ForeColor="White" HorizontalAlign="Center" Font-Underline="false" />
                    <PagerStyle BackColor="White" ForeColor="#0d6efd" HorizontalAlign="Center" />
                    <RowStyle ForeColor="#0d6efd" />
                    <SelectedRowStyle BackColor="#669999" Font-Bold="True" ForeColor="White" />
                </asp:GridView>
            </div>
        </div>
</asp:Content>

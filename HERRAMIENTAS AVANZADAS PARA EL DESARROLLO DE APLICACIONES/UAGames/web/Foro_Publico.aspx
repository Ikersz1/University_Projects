<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Foro_Publico.aspx.cs" Inherits="web.Foro_Publico" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height:625px;">
        <div class="showGrid">
            <h1>Página de Foro</h1>
            <br />
            Selecciona el foro&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:DropDownList runat="server" ID="DForo"></asp:DropDownList>
            <br />
            <asp:Button ID="BForo" runat="server" Text="Buscar Temas" OnClick="BuscarTemas" CssClass="btn btn-primary"/>
            <br />
            <asp:Label ID="LTema" runat="server" Text ="Selecciona el tema del foro"  Visible="false"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <asp:DropDownList runat="server" ID="DTema" Visible="false"></asp:DropDownList>
            <br />
            <asp:Button ID="BBuscar" runat="server" CssClass="btn btn-primary" Text="Buscar" OnClick="Buscar" Visible="false"/>
            <div class="table-responsive">
                <asp:GridView ID="GridView1" runat="server" CssClass="grid" HorizontalAlign="Center" CellPadding="5" PageSize="5" AllowPaging="True" OnPageIndexChanging="ChangePage">
                    <FooterStyle BackColor="White" ForeColor="#0d6efd" />
                    <HeaderStyle BackColor="#0d6efd" Font-Bold="True" ForeColor="White" HorizontalAlign="Center" Font-Underline="false" />
                    <PagerStyle BackColor="White" ForeColor="#0d6efd" HorizontalAlign="Center" />
                    <RowStyle ForeColor="#0d6efd" />
                    <SelectedRowStyle BackColor="#669999" Font-Bold="True" ForeColor="White" />
                </asp:GridView>
            </div>
            <br />
            <h2>Hacer una publicación:</h2>
            <br />
            Texto: &nbsp;&nbsp;&nbsp;&nbsp;
            <br />
            <asp:TextBox ID="TTexto" runat="server" Height="90px" Width="300px" TextMode="MultiLine"></asp:TextBox>
            <br />
            <br />
            <asp:Button ID="BPublicar" CssClass="btn btn-primary" runat="server" Text="Publicar" OnClick="Publicar"/>
            <br />
            <asp:Label ID="LResultado" runat="server" Text=" "></asp:Label>
        </div>
    </div>
</asp:Content>

<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Compras.aspx.cs" Inherits="web.Compras" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto" style="min-height: 625px;">
        <div class="row">
            <center>
                <h1>Historial de compras</h1>
                <br />
                <br />

                <asp:GridView ID="GridView1" runat="server" CssClass="grid" AutoGenerateColumns="False" BackColor="White" BorderColor="#999999" BorderStyle="None" BorderWidth="1px" CellPadding="3" DataKeyNames="Número de pedido" DataSourceID="UAGAMES" GridLines="Vertical" AllowPaging="True">
            <AlternatingRowStyle BackColor="#DCDCDC" />
            <Columns>
                <asp:BoundField DataField="Usuario" HeaderText="Usuario" SortExpression="Usuario" />
                <asp:BoundField DataField="Fecha compra" HeaderText="Fecha compra" SortExpression="Fecha compra" />
                <asp:BoundField DataField="Número de pedido" HeaderText="Número de pedido" InsertVisible="False" ReadOnly="True" SortExpression="Número de pedido" />
            </Columns>
            <FooterStyle BackColor="White" ForeColor="#0d6efd" />
                <HeaderStyle BackColor="#0d6efd" Font-Bold="True" ForeColor="White" HorizontalAlign="Center" Font-Underline="false" />
                <PagerStyle BackColor="White" ForeColor="#0d6efd" HorizontalAlign="Center" />
                <RowStyle ForeColor="#0d6efd" />
                <SelectedRowStyle BackColor="#669999" Font-Bold="True" ForeColor="White" />
                <SortedAscendingCellStyle BackColor="#F1F1F1" />
                <SortedAscendingHeaderStyle BackColor="#007DBB" />
                <SortedDescendingCellStyle BackColor="#CAC9C9" />
                <SortedDescendingHeaderStyle BackColor="#00547E" />
    </asp:GridView>
    <asp:SqlDataSource ID="UAGAMES" runat="server" ConnectionString="<%$ ConnectionStrings:miconexion %>" SelectCommand="SELECT Usuario.nick AS 'Usuario', Compra.fecha AS 'Fecha compra', Compra.id AS 'Número de pedido' FROM Compra INNER JOIN Usuario ON Compra.usuarioID = Usuario.id"></asp:SqlDataSource>
            </center>
        
   </div>
        </div>
</asp:Content>




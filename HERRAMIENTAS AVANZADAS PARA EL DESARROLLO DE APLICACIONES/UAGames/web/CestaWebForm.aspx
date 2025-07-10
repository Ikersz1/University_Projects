<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="CestaWebForm.aspx.cs" Inherits="web.CestaWebForm" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>

<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
 
    <div class="container bg-light overflow-auto" style="min-height: 625px;">
        <div class="showGrid">
            <center>
                <h2>Tu cesta</h2>
            
            <asp:GridView ID="cestaTable" runat="server" CssClass="text-primary" AutoGenerateColumns="False" BorderStyle="None" BackColor="White" BorderWidth="2px"
                DataKeyNames="titulo" PageSize="5" AllowPaging="True" CellPadding="5"
                EmptyDataText="Ups, no se ha encontrado nada en la cesta."
                OnPageIndexChanging="changePageCestaTable"
                OnRowEditing="clickRowEditCesta"
                OnRowCancelingEdit="clickRowCancelCesta"
                OnRowUpdating="clickRowUpdateCesta"
                OnRowDeleting="clickRowDeleteCesta"
                GridLines="Horizontal">

                <Columns>
                    <asp:BoundField DataField="usuarioID" HeaderText="VideojuegoID" ReadOnly="True" SortExpression="VideojuegoID" />
                    <asp:BoundField DataField="videojuegoID" HeaderText="UsuarioID" ReadOnly="True" SortExpression="UsuarioID" />
                    <asp:BoundField DataField="titulo" HeaderText="Titulo" ReadOnly="True" SortExpression="Titulo" />
                    <asp:BoundField DataField="fecha" HeaderText="Fecha" ReadOnly="True" SortExpression="Fecha" />
                    <asp:BoundField DataField="cantidad" HeaderText="Cantidad" ReadOnly="True" SortExpression="Cantidad" />
                    <asp:BoundField DataField="total" HeaderText="Total" ReadOnly="True" SortExpression="Total" />


                    <asp:CommandField HeaderText=" " ButtonType="Image" CancelImageUrl="~/assets/imagenes/iconos/cancel.png" EditImageUrl="~/assets/imagenes/iconos/edit.png"
                        ShowEditButton="false" ShowDeleteButton="True" ShowCancelButton="True" UpdateImageUrl="~/assets/imagenes/iconos/check.png"
                        DeleteImageUrl="~/assets/imagenes/iconos/trash.png">
                        <ControlStyle Height="20px" Width="20px" CssClass="botones" />
                    </asp:CommandField>

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
            

            <div class="precioTotalContainer">
                <div class="precioTotalBox">
                    <center>                   
                        Total cesta:<asp:Label ID="precioTotalLabel" runat="server" Style="margin-left: 60px;"></asp:Label>
                    </center>
                </div>
            </div>
            <asp:Label ID="Resultado" runat="server" Text =" " />&nbsp;
            <div>
                
                    <asp:Button id="comprar" CssClass="btn btn-primary" Text="Comprar" runat="server" OnClick="ComprarClick" NavigateUrl="Foro.aspx"/>
                
            </div>
            </center>
        </div>
    </div>



<style>
    .precioTotalContainer {
  margin-right: 20px;
    background-color: #dddddd;
    padding: 10px;
    border-radius: 5px;
    max-width: 300px;
    margin: 20px auto;
    }

.precioTotalBox {
  display: inline-block;
  padding: 10px;
  background-color: #f2f2f2;
  border: 1px solid #ddd;
  border-radius: 5px;

}

#precioTotalLabel {
  color: #333;
  font-size: 24px;
  font-weight: bold;
  text-align: center;
}
</style>

    
</asp:Content>

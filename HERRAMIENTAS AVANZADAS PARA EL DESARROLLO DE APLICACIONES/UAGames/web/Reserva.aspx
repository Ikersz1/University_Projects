<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Reserva.aspx.cs" Inherits="web.Reserva" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
    <%@ Register Assembly="AjaxControlToolkit" Namespace="AjaxControlToolkit" TagPrefix="ajaxToolkit" %>
    <link rel="stylesheet" href="assets/css/Rating.css" />
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <asp:ScriptManager ID="asm" runat="server" />
    <div class="container bg-light overflow-auto align-self-md-center" style="min-height: 599px;">
        <div class="d-flex flex-row text-primary mt-3">
            <div class="mt-6 align-items-xxl-start">
                <h2>Reservas</h2>
                <asp:GridView ID="reservaTable" runat="server" CssClass="text-primary" AutoGenerateColumns="False" BorderStyle="None" BackColor="White" BorderWidth="2px" PageSize="5" AllowPaging="True" CellPadding="5"
                    EmptyDataText="Ups, no se ha encontrado ninguna reserva."
                    OnPageIndexChanging="changePageReservaTable"
                    OnRowEditing="clickRowEditReserva"
                    OnRowCancelingEdit="clickRowCancelReserva"
                    OnRowUpdating="clickRowUpdateReserva"
                    OnRowDeleting="clickRowDeleteReserva"
                    GridLines="Horizontal">

                    <Columns>
                        <asp:BoundField DataField="id" HeaderText="ID" ReadOnly="True" SortExpression="Id" Visible="false">
                            <ItemStyle HorizontalAlign="Center" />
                        </asp:BoundField>
                        <asp:BoundField DataField="videojuego" HeaderText="Videojuego" SortExpression="Videojuego">
                            <ItemStyle HorizontalAlign="Center" />
                        </asp:BoundField>
                        <asp:BoundField DataField="pagado" HeaderText="Pagado" SortExpression="Pagado">
                            <ItemStyle HorizontalAlign="Center" />
                        </asp:BoundField>
                        <asp:BoundField DataField="fechaEntrega" HeaderText="Fecha de entrega" SortExpression="FechaEntrega">
                            <ItemStyle HorizontalAlign="Center" />
                        </asp:BoundField>
                        <asp:BoundField DataField="fecha" HeaderText="Fecha" SortExpression="Fecha">
                            <ItemStyle HorizontalAlign="Center" />
                        </asp:BoundField>
                        <asp:CommandField HeaderText="Acciones" ButtonType="Image" CancelImageUrl="~/assets/imagenes/iconos/cancel.png" EditImageUrl="~/assets/imagenes/iconos/edit.png"
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
                <div>
                <asp:Button ID="pagarReserva" runat="server" Text="Pagar" CssClass="btn btn-primary" OnClick="mostrarPagar_click" Visible="true" />
                <asp:TextBox ID="nombreJuego" runat="server" Visible="false" placeholder="Titulo..." CssClass="mb-2" Style="width: 300px;"></asp:TextBox>
                <asp:TextBox ID="pagar" runat="server" Visible="false" placeholder="Importe a pagar..." CssClass="mb-2"></asp:TextBox>
                    </div>
                <div>
                    &nbsp&nbsp
                <asp:ImageButton ID="confirmar" ImageUrl="assets/imagenes/iconos/check.png" runat="server" Visible="false" OnClick="Confirmar_click" ValidationGroup="validationGroup" Width="50px" Height="50px" />
                    <asp:ImageButton ID="cancelar" ImageUrl="assets/imagenes/iconos/cancel.png" runat="server" Visible="false" OnClick="Cancelar_click" Width="50px" Height="50px" />
                </div>
                <br />
            </div>
            <asp:RequiredFieldValidator ID="comentarioReviewValidator" runat="server" ControlToValidate="nombreJuego" ErrorMessage="* El campo titulo es obligatorio." ValidationGroup="validationGroup" />
             <asp:RequiredFieldValidator ID="pagarValidator" runat="server" ControlToValidate="pagar" ErrorMessage="* El campo pagado es obligatorio." ValidationGroup="validationGroup" />
                <div>
                    &nbsp&nbsp
                </div>
            </div>
</asp:Content>


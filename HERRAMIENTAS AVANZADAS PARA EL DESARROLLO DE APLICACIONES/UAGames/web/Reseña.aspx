<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Reseña.aspx.cs" Inherits="web.Reseña" %>


<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
    <%@ Register Assembly="AjaxControlToolkit" Namespace="AjaxControlToolkit" TagPrefix="ajaxToolkit" %>
    <link rel="stylesheet" href="assets/css/Rating.css" />
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <asp:ScriptManager ID="asm" runat="server" />
    <div class="container bg-light overflow-auto" style="min-height: 599px;">
        <div class="d-flex flex-row text-primary mt-3">
            <div class="mt-6 align-items-xxl-start">
                <asp:Button CssClass="btn btn-primary" ID="volver" Text="Volver" runat="server" OnClick="Volver_click" />
            </div>
            <div class="mt-5">
                <asp:Image ID="videojuegoImagen" runat="server" Style="height: 30rem; width: 30rem; margin-right: 40px; object-fit: cover; object-position: left;" />
            </div>
            <div class="mt-4">
                <div class="mt-2">
                    <asp:Label ID="tituloLabel" runat="server" Style="font-size: 45px; font-weight: bold;"></asp:Label>
                    <asp:Label ID="UsuarioLabel" runat="server" Style="font-size: 45px; font-weight: bold;" />
                    <div class="border-top border-primary mt-2">
                    </div>
                    <div>
                        <h4 class="mt-1">Fecha:
                        <asp:Label ID="fechaLabel" runat="server" /></h4>
                    </div>
                    <div>
                        <h4 class="mt-1  border-primary border-top">Reseña del usuario: </h4>
                        <asp:Label ID="comentarioLabel" runat="server" Font-Size="30px" />
                    </div>
                    <div>
                        <h4 class="mt-1 border-primary border-top">
                            <br />
                            <ajaxToolkit:Rating ID="Puntuacion" runat="server" MaxRating="5" Font-Size="45px"
                                EmptyStarCssClass="emptyRatingStar" FilledStarCssClass="filledRatingStar" StarCssClass="emptyRatingStar" WaitingStarCssClass="emptyRatingStar" ReadOnly="true" />
                        </h4>

                    </div>
                </div>
                <!--BOTONES-->
            </div>
            <div class="mt-6 mx-5 d-flex">
                <div class="mt-8 align-top">
                    <asp:ImageButton ID="Editar" ImageUrl="assets/imagenes/iconos/edit.png" runat="server" Visible="false" OnClick="Editar_click" ValidationGroup="validationGroup" Width="50px" Height="50px" />
                    &nbsp&nbsp<asp:ImageButton ID="Eliminar" ImageUrl="assets/imagenes/iconos/trash.png" runat="server" Visible="false" OnClick="Eliminar_click" Width="50px" Height="50px" />
                </div>
            </div>
            </>
        </div>
        <div class="mt-8 text-right">
            <br />
            <div>
                <asp:Literal ID="textoPuntuacion" Text="Puntuacion:" runat="server" Visible="false" />
            </div>
            <div class="d-flex align-items-center">
                <asp:DropDownList ID="puntuacionReview" AutoPostBack="false" runat="server" CssClass="mb-2 px-4 text-center" Visible="false" Style="height: 30px;">
                    <Items>
                        <asp:ListItem Text="1" Value="1" />
                        <asp:ListItem Text="2" Value="2" />
                        <asp:ListItem Text="3" Value="3" />
                        <asp:ListItem Text="4" Value="4" />
                        <asp:ListItem Text="5" Value="5" />
                    </Items>
                </asp:DropDownList>
                <asp:TextBox ID="comentarioReview" runat="server" Visible="false" placeholder="Comentario..." CssClass="mb-2" Style="width: 300px;"></asp:TextBox>
                <div>
                    &nbsp&nbsp
                <asp:ImageButton ID="confirmar" ImageUrl="assets/imagenes/iconos/check.png" runat="server" Visible="false" OnClick="Confirmar_click" ValidationGroup="validationGroup" Width="50px" Height="50px" />
                    <asp:ImageButton ID="cancelar" ImageUrl="assets/imagenes/iconos/cancel.png" runat="server" Visible="false" OnClick="Cancelar_click" Width="50px" Height="50px" />
                </div>
                <br />
            </div>
            <asp:RequiredFieldValidator ID="comentarioReviewValidator" runat="server" ControlToValidate="comentarioReview" ErrorMessage="* El campo Comentario es obligatorio." ValidationGroup="validationGroup" />
        </div>
    </div>
</asp:Content>

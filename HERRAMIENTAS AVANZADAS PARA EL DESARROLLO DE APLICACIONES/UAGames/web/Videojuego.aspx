<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Videojuego.aspx.cs" Inherits="web.Videojuego" EnableEventValidation="true" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">

    <%@ Register Assembly="AjaxControlToolkit" Namespace="AjaxControlToolkit" TagPrefix="ajaxToolkit" %>
    <link rel="stylesheet" href="assets/css/Rating.css" />
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <asp:ScriptManager ID="asm" runat="server" />
    <div class="container bg-light overflow-auto" style="min-height: 599px;">
        <div class="d-flex flex-row text-primary mt-3">
            <div class="mt-3">
                <asp:Image ID="videojuegoImagen" runat="server" Style="height: 500px; margin-right: 20px; object-fit: cover; object-position: left;" />
            </div>
            <div class="mt-2">
                <asp:Label ID="tituloLabel" runat="server" Style="font-size: 35px; font-weight: bold;"></asp:Label>
                <div class="border-top border-primary mt-2">
                    <h5 class="mt-3">Productora: </h5>
                    <asp:LinkButton ID="productoraLink" runat="server" CssClass="text-primary" />
                </div>
                <div>
                    <h5 class="mt-1">Categoria: </h5>
                    <asp:LinkButton ID="categoriaLink" runat="server" CssClass="text-primary" />
                </div>
                <div>
                    <h5 class="mt-1">Fecha lanzamiento: </h5>
                    <asp:Label ID="fechaLabel" runat="server" />
                </div>
                <div>
                    <h5 class="mt-1">Plataforma: </h5>
                    <asp:Label ID="plataformaLabel" runat="server" />
                </div>
                <div>
                    <h5 class="mt-1">Descripcion: </h5>
                    <asp:Label ID="descripcionLabel" runat="server" />
                </div>
                <div>
                    <h5 class="mt-1">Precio: </h5>
                    <asp:Label ID="precioLabel" runat="server" />
                </div>
                <!--BOTONES-->
                <div class="text-center d-flex flex-row mt-3">
                    <asp:ImageButton ID="addCart" runat="server" AlternateText="Add Cart" ImageAlign="middle" Style="height: 3rem;" CssClass="bg-primary mx-3 rounded"
                        ImageUrl="assets/imagenes/iconos/cartAdd.png" OnClientClick="clickAddCart" OnClick="clickAddCart" CommandArgument='<%# Eval("id") %>' />
                    <asp:ImageButton ID="addList" runat="server" AlternateText="Add List" ImageAlign="middle" Style="height: 3rem;" CssClass="bg-primary rounded"
                        ImageUrl="assets/imagenes/iconos/addList.png" OnClientClick="clickAddList" OnClick="clickAddList" CommandArgument='<%# Eval("id") %>' />
                </div>
            </div>
            <div class="mt-3 mx-5">
                <asp:ListView ID="ofertaDisplay" runat="server">
                    <EmptyDataTemplate>
                        <span></span>
                    </EmptyDataTemplate>
                    <LayoutTemplate>
                        <div>
                            <table runat="server">
                                <tr runat="server" id="groupPlaceholder">
                                </tr>
                            </table>
                        </div>
                    </LayoutTemplate>
                    <GroupTemplate>
                        <tr runat="server" id="ofertarow" class="mx-4">
                            <td runat="server" id="itemPlaceholder"></td>
                        </tr>
                    </GroupTemplate>
                    <ItemTemplate>
                        <td class="mx-4">
                            <div class="card bg-primary text-white mx-3 my-2 border-0">
                                <div class="row">
                                    <div class="card-body mt-3">
                                        <h1 class="card-title"><%# Eval("nombre") %></h1>
                                        <h3>Descuento: <%# Eval("descuento") %>%</h3>
                                        <h3>Desde: <%# Eval("fecha_inicio") %></h3>
                                        <h3>Hasta: <%# Eval("fecha_fin") %></h3>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="card bg-primary text-white mx-3 my-2 border-0">
                                <h1 class="card-title">Precio oferta: <%# Eval("nuevoPrecio") %> €</h1>
                            </div>
                        </td>
                    </ItemTemplate>
                </asp:ListView>
            </div>
        </div>
        <br />
        <div>
            <div>
                <asp:Literal ID="textoPuntuacion" Text="Puntuacion:" runat="server" Visible="false" />
            </div>
            <div class="d-flex align-items-center">
                <asp:DropDownList ID="puntuacionReview" AutoPostBack="false" runat="server" CssClass="mb-1 px-4 text-center" Visible="false" Style="height: 30px;">
                    <Items>
                        <asp:ListItem Text="1" Value="1" />
                        <asp:ListItem Text="2" Value="2" />
                        <asp:ListItem Text="3" Value="3" />
                        <asp:ListItem Text="4" Value="4" />
                        <asp:ListItem Text="5" Value="5" />
                    </Items>
                </asp:DropDownList>

                <asp:TextBox ID="comentarioReview" runat="server" Visible="false" placeholder="Comentario..." CssClass="mb-1 px-2" Style="width: 600px; height: 30px;"></asp:TextBox>
                <asp:Button CssClass="btn btn-primary" ID="añadirReview" Text="Nueva reseña" runat="server" Visible="true" OnClick="añadirReview_Click" />

                <div class="container">
                    <button id="reservas" runat="server" type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmacionPopup" style="display: none">Reservar</button>
                    <div id="confirmacionPopup" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Confirmación de Reserva</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Estás seguro de que deseas reservar este videojuego?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" onclick="reservarVideojuego()">Reservar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <asp:Button CssClass="btn btn-primary" ID="reservarButton" Text="Reservar" runat="server" OnClick="ReservarVideojuego_Click" Style="display: none;" />
                </div>

                <script>
                    function reservarVideojuego() {
                        // Lógica para realizar la reserva del videojuego

                        // Cerrar el popup después de realizar la reserva
                        $('#confirmacionPopup').modal('hide');
                        document.getElementById('<%= reservarButton.ClientID %>').click();
                    }
                </script>
            </div>

            <div>
                &nbsp&nbsp
                <asp:ImageButton ID="crearReview" ImageUrl="assets/imagenes/iconos/check.png" runat="server" Visible="false" OnClick="crearReview_click" ValidationGroup="validationGroup" Width="50px" Height="50px" />
                <asp:ImageButton ID="cancelar" ImageUrl="assets/imagenes/iconos/cancel.png" runat="server" Visible="false" OnClick="cancelarReview_click" Width="50px" Height="50px" />
            </div>
            <asp:RequiredFieldValidator ID="comentarioReviewValidator" runat="server" ControlToValidate="comentarioReview" ErrorMessage="* El campo Comentario es obligatorio." ValidationGroup="validationGroup" />
            <asp:RequiredFieldValidator ID="reservaCreadaValidator" runat="server" ControlToValidate="ordenar" ErrorMessage="Ya has realizado una reserva de este videojuego." Visible="false" />
        </div>
        <div>
            <asp:DropDownList ID="ordenar" AutoPostBack="false" runat="server" CssClass="mb-2  px-5">
                <Items>
                    <asp:ListItem Text="Ordenar por:" Value="" />
                    <asp:ListItem Text="Puntuación" Value="puntuacion" />
                    <asp:ListItem Text="Fecha" Value="fecha" />
                </Items>
            </asp:DropDownList>
            &nbsp&nbsp&nbsp&nbsp
            <asp:Button CssClass="btn btn-primary" ID="filtrarReview" Text="Filtrar" runat="server" Visible="true" OnClick="filtrarReview_click" />
        </div>
        <!--REVIEWS-->
        <div class="row d-flex flex-row mt-3">
            <h1>Valoraciones</h1>
        </div>
        <asp:ListView ID="listViewReviews" runat="server">
            <EmptyDataTemplate>
                <span>No hay valoraciones de este videojuego.</span>
            </EmptyDataTemplate>
            <LayoutTemplate>
                <div>
                    <table runat="server">
                        <tr runat="server" id="groupPlaceholder">
                        </tr>
                    </table>
                    <div class="text-left">
                        <asp:DataPager runat="server" ID="DataPager" PageSize="3" HorizontalAlign="Center" CssClass="text-center text-primary">
                            <Fields>
                                <asp:NextPreviousPagerField ButtonType="Button" ShowNextPageButton="False" ShowPreviousPageButton="True" ButtonCssClass="btn btn-primary"
                                    PreviousPageText=" < " FirstPageText=" << " />
                                <asp:NumericPagerField />
                                <asp:NextPreviousPagerField ButtonType="Button" ShowNextPageButton="True" ShowPreviousPageButton="False" ButtonCssClass="btn btn-primary"
                                    NextPageText=" > " LastPageText=" >> " />
                            </Fields>
                        </asp:DataPager>
                    </div>

                </div>
            </LayoutTemplate>
            <GroupTemplate>
                <tr runat="server" id="videojuegoRow" class="mx-4">
                    <td runat="server" id="itemPlaceholder"></td>
                </tr>
            </GroupTemplate>
            <ItemTemplate>
                <asp:HyperLink ID="HyperLink1" runat="server" NavigateUrl='<%#$"Reseña.aspx?id={Eval("id")}" %>'>
                    <div class="row fa-align-center">
                        <div class="card col-md-8 border-3">
                            <div class="card-body align-content-center">
                                <div class="row">
                                    <div class="col-md-10 mb-2" style="font-size: 25px; text-decoration: none;">
                                        <%# Eval("comentario") %>
                                    </div>
                                    <div class="col-md-3">
                                        <h2>
                                            <ajaxToolkit:Rating ID="ratingAvg" runat="server" CurrentRating='<%# Eval("puntuacion") %>' MaxRating="5"
                                                EmptyStarCssClass="emptyRatingStar" FilledStarCssClass="filledRatingStar" StarCssClass="emptyRatingStar" WaitingStarCssClass="emptyRatingStar" ReadOnly="true" />
                                        </h2>
                                    </div>
                                    <div class="col-md-3">
                                        <%# Convert.ToDateTime(Eval("fecha")).ToString("dd/MM/yyyy") %>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </asp:HyperLink>
                <div class="col-md-2"></div>
                <br />
            </ItemTemplate>
        </asp:ListView>
    </div>
</asp:Content>

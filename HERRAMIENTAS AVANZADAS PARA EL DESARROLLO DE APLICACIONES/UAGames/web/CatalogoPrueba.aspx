<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="CatalogoPrueba.aspx.cs" Inherits="web.CatalogoPrueba" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <main>

    
    <asp:ListView ID="VideojuegosListView" runat="server">
        <LayoutTemplate>
          <table runat="server">
                    <tr runat="server">
                        <td runat="server">
                            <table id="groupPlaceholderContainer" runat="server" border="0" style="">
                                <tr id="groupPlaceholder" runat="server">
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr runat="server">
                        <td runat="server" style="">
          <asp:DataPager runat="server" ID="CatalogoDataPager" PageSize="10">
            <Fields>
                <asp:NumericPagerField />
              <asp:NextPreviousPagerField ShowFirstPageButton="true" ShowLastPageButton="true"
                FirstPageText="|<< " LastPageText=" >>|"
                NextPageText=" > " PreviousPageText=" < " />
            </Fields>
          </asp:DataPager>
         </td>
         </tr>
        </table>
        </LayoutTemplate>
        <ItemTemplate>
          <td runat="server" style="display: flex; margin-bottom: 50px">
            <asp:HyperLink ID="videojuegoImageLink" runat="server" ImageUrl='<%# Eval("imagen") %>' ImageHeight="200" NavigateUrl='<%#$"Videojuego.aspx?titulo={Eval("titulo")}" %>' Style="height: 200px; margin-right: 20px; object-fit: cover; object-position: left;" />

            <div id="videojuegoInfo" style="min-width: 700px">
                <asp:HyperLink ID="videojuegoLink" runat="server" Text='<%# Eval("titulo") %>' NavigateUrl='<%#$"Videojuego.aspx?titulo={Eval("titulo")}" %>' Font-Underline="false" Style="font-size: 20px; font-weight: bold; margin-right: 10%;" />
                <hr />
                <div class="detallesVideojuego">
                    <asp:Label ID="ProductoraLabel" runat="server" Text="Productora: " />
                    <asp:LinkButton ID="ProductoraLink" runat="server"  Text='<%# Eval("productora") %>' CommandArgument='<%# Eval("productora") %>' OnClick="clickProductora" />
                </div>
                <div>
                    <asp:Label ID="CategoriaLabel" runat="server" Text="Categoria: " />
                    <asp:LinkButton ID="CategoriaLink" runat="server" Text='<%# Eval("categoria") %>' CommandArgument='<%# Eval("categoria") %>' OnClick="clickCategoria" />
                </div>
                <div>
                     <asp:Label ID="PlataformaLabel" runat="server" Text="Plataforma: " />
                    <asp:LinkButton ID="PlataformaLink" runat="server" Text='<%# Eval("plataforma") %>' CommandArgument='<%# Eval("plataforma") %>' OnClick="clickPlataforma" />
                </div>
                <br />
                <div class="compraVideojuego">
                    <asp:Button ID="ListaDeseosButton" CssClass="button" runat="server" Text="🔖 Lista de Deseos" CommandArgument='<%# Eval("titulo") %>' OnClientClick="clickAddToListaDeseos" OnClick="clickAddToListaDeseos" />
                    <asp:Button ID="ReviewButton" CssClass="button" runat="server" Text="🖊️ Ver reviews" CommandArgument='<%# Eval("titulo") %>' OnClientClick="clickSeeReviews" OnClick="clickSeeReviews" />
                </div>
            </div>
            </td>
        </ItemTemplate>
        <EditItemTemplate>
         <td runat="server">
                    titulo:
                    <asp:TextBox ID="tituloTextBox" runat="server" Text='<%# Bind("titulo") %>' />
                    <br />
                    plataforma:
                    <asp:TextBox ID="PlataformaTextBox" runat="server" Text='<%# Bind("plataforma") %>' />
                    <br />
                    imagen:
                    <asp:TextBox ID="ImagenTextBox" runat="server" Text='<%# Bind("imagen") %>' />
                    <br />
                    precio:
                    <asp:TextBox ID="PrecioTextBox" runat="server" Text='<%# Bind("precio") %>' />
                    <br />
                    productora:
                    <asp:TextBox ID="ProductoraTextBox" runat="server" Text='<%# Bind("productora") %>' />
                    <br />
                    categoria:
                    <asp:TextBox ID="CategoriaTextBox" runat="server" Text='<%# Bind("categoria") %>' />
                    <br />
                </td>
        </EditItemTemplate>
        <EmptyDataTemplate>
                <table runat="server">
                    <tr>
                        <td>No se han encontrado videojuegos.</td>
                    </tr>
                </table>
            </EmptyDataTemplate>
            <EmptyItemTemplate>
                <td runat="server" />
            </EmptyItemTemplate>
            <GroupTemplate>
                <tr id="itemPlaceholderContainer" runat="server">
                    <td id="itemPlaceholder" runat="server"></td>
                </tr>
            </GroupTemplate>
        <InsertItemTemplate>
                <td runat="server">
                    titulo:
                    <asp:TextBox ID="tituloTextBox" runat="server" Text='<%# Bind("titulo") %>' />
                    <br />
                    plataforma:
                    <asp:TextBox ID="PlataformaTextBox" runat="server" Text='<%# Bind("plataforma") %>' />
                    <br />
                    imagen:
                    <asp:TextBox ID="ImagenTextBox" runat="server" Text='<%# Bind("imagen") %>' />
                    <br />
                    precio:
                    <asp:TextBox ID="PrecioTextBox" runat="server" Text='<%# Bind("precio") %>' />
                    <br />
                    productora:
                    <asp:TextBox ID="ProductoraTextBox" runat="server" Text='<%# Bind("productora") %>' />
                    <br />
                    categoria:
                    <asp:TextBox ID="CategoriaTextBox" runat="server" Text='<%# Bind("categoria") %>' />
                    <br />
                </td>
            </InsertItemTemplate>
      </asp:ListView>
        </main>

</asp:Content>

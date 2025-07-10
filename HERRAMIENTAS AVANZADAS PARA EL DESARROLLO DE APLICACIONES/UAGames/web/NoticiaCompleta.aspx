<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="NoticiaCompleta.aspx.cs" Inherits="web.NoticiaCompleta" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
    <style>
    .list {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .list-img {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 10px;
    }

    .list-img img {
        max-width: 50%;
        height: auto;
        border-radius: 5px;
    }

    .list-content {
        text-align: center;
    }

    .list-content h1 {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .list-content p.date {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .list-content p.content {
        font-size: 16px;
        line-height: 1.5;
        margin-bottom: 20px;
        white-space: pre-line;
    }
</style>

</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto list-container" style="min-height:300px;">
    <asp:ListView ID="ListView1" runat="server">
        
        <ItemTemplate>
            <div class="list">
                <div class="list-img">
                    <img src='<%#Eval("imagen") %>' />
                </div>
                <div class="list-content">
                    <h1><%#Eval("titulo") %></h1>
                    <p class="date"><%#Eval("fecha_public") %></p>
                    <p class="content"><%#Eval("contenido") %></p>
                </div>
            </div>
        </ItemTemplate>
       
    </asp:ListView>
         </div>
</asp:Content>

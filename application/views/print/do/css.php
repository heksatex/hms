<style>
    * {
        box-sizing: border-box;
    }
    body {
        margin:  30px 10px 0px 10px;
        font-family: "Times New Roman", Times, serif;
    }
    .title {
        text-align: center;
        text-transform: uppercase;
        margin-top: 20px;
    }
    .title span {
        font-weight: 800;
        border-width: thin;
        font-size: 16px;
        text-decoration: underline;
        padding-top: 1px;
    }
    .nosjprint{
        font-size: 14px;
        font-weight: 800;
    }
    .item-right {
        display: flex;
        justify-content: flex-end;
    }
    .row-1 {
        width: 100px;
        font-weight: 600;
    }
    .deskripsi {
        display: flex;
        /*height:5vh;*/
        align-items: end;
    }
    hr {
        margin: 5px 0 5px 0;
        border-top: 1px dotted gray;
    }
    .footer{
        display: flex;
        justify-content: space-between;
    }
    .footer .divs{
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 15vh;
        font-size: 12px;
    }
    .total_pcs{
        width: 89%
    }
    p {
        /*line-height: 2px;*/
        margin: 0;
    }
    table tbody{
        font-size: 12px;
    }
    table thead{
        font-size: 13px;
    }
    tfoot   {
        display: table-footer-group;
    }
    @media print {
        html,body{
            font-family: Tahoma, sans-serif;
        }
        #pg {
            page-break-after: always;
        }
        .deskripsi {
            display: flex;
            /*height:3vh;*/
            align-items: end;
        }
        p{
            margin: 0;
        }
        .border_table {
            border: 1px solid ;
        }
        table,
        th,
        td {
            text-align: left;
            border: 1px solid black;
            border-collapse: collapse;
        }
    }
    #dialogoverlay{
        display:none;
        background-color:#ffffff;
        opacity:0.7;
        width:100%;
        top:0px;
        left:0px;
        position:fixed;
        z-index:10;
    }
    button{
        padding:4px 7px;
    }
    #dialogbox{
        display:none;
        position:fixed;
        background:#000000;
        width:560px;
        z-index:10;
        border-radius:7px;
    }
    #dialogbox div{
        background:#ffffff;
        margin:8px;
    }
    #dialogbox #dialogboxhead{
        background: #666;
        font-size:19px;
        padding:10px;
        color:#CCC;
    }
    #dialogbox #dialogboxbody{
        background:#333;
        padding:20px;
        color:#FFF;
    }
    #dialogbox #dialogboxfoot{
        background: #666;
        padding:10px;
        text-align:right;
    }

    #box{
        position:absolute;
        left:360px;
        top:180px;
    }
</style>

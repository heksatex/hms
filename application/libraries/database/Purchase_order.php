<?php

class Purchase_order {
    const table = "purchase_order";
    const fields = [
        "id"=>"id",
        "noPo"=>"no_po",
        "supplier"=>"supplier",
        "note"=>"note",
        "orderDate"=>"order_date",
        "createDate"=>"create_date",
        "status"=>"status",
        "currency"=>"currency",
        "nilaiCurrency"=>"nilai_currency",
        "jenis"=>"jenis",
        "cfbManual"=>"cfb_manual",
        "noValue"=>"no_value",
        "validatedBy"=>"validated_by",
        "total"=>"total",
        "dppLain"=>"dpp_lain",
        "footNote"=>"foot_note",
        "payment"=>"payment"
    ];
}
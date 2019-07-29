<?xml version='1.0' standalone='yes'?>
<foxydata>
    <datafeed_version>XML FoxyCart Version 0.6</datafeed_version>
    <transactions>
        <transaction>
            <id>$OrderID</id>
            <store_id>9</store_id>
            <store_version>2.0</store_version>
            <is_test>1</is_test>
            <is_hidden>0</is_hidden>
            <data_is_fed>0</data_is_fed>
            <transaction_date>$TransactionDate</transaction_date>
            <processor_response>Authorize.net Transaction ID:2154082729</processor_response>
            <customer_id>122</customer_id>
            <is_anonymous>0</is_anonymous>
            <minfraud_score>0</minfraud_score>
            <customer_first_name>John</customer_first_name>
            <customer_last_name>Doe</customer_last_name>
            <customer_company>Your Company</customer_company>
            <customer_address1>12345 Any Street</customer_address1>
            <customer_address2></customer_address2>
            <customer_city>Any City</customer_city>
            <customer_state>TN</customer_state>
            <customer_postal_code>37013</customer_postal_code>
            <customer_country>US</customer_country>
            <customer_phone>(123) 456-7890</customer_phone>
            <customer_email>$Email</customer_email>
            <customer_ip>71.228.237.177</customer_ip>
            <shipping_first_name>John</shipping_first_name>
            <shipping_last_name>Doe</shipping_last_name>
            <shipping_company></shipping_company>
            <shipping_address1>1234 Any Street</shipping_address1>
            <shipping_address2></shipping_address2>
            <shipping_city>Some City</shipping_city>
            <shipping_state>TN</shipping_state>
            <shipping_postal_code>37013</shipping_postal_code>
            <shipping_country>US</shipping_country>
            <shipping_phone></shipping_phone>
            <shipping_service_description>UPS: Ground</shipping_service_description>
            <purchase_order></purchase_order>
            <cc_number_masked>xxxxxxxxxxxx4242</cc_number_masked>
            <cc_type>Visa</cc_type>
            <cc_exp_month>08</cc_exp_month>
            <cc_exp_year>2013</cc_exp_year>
            <cc_start_date_month>06</cc_start_date_month>
            <cc_start_date_year>2008</cc_start_date_year>
            <cc_issue_number>01</cc_issue_number>
            <product_total>20.00</product_total>
            <tax_total>0.00</tax_total>
            <shipping_total>4.38</shipping_total>
            <order_total>24.38</order_total>
            <order_total>24.38</order_total>
            <payment_gateway_type>authorize</payment_gateway_type>
            <status>approved</status>
            <customer_password>$HashedPassword</customer_password>
            <customer_password_salt>$Salt</customer_password_salt>
            <customer_password_hash_type>$HashType</customer_password_hash_type>
            <customer_password_hash_config>48</customer_password_hash_config>
            <custom_fields>
                <custom_field>
                    <custom_field_name>My_Cool_Text</custom_field_name>
                    <custom_field_value>Value123</custom_field_value>
                    <custom_field_is_hidden>1</custom_field_is_hidden>
                </custom_field>
                <custom_field>
                    <custom_field_name>Another_Custom_Field</custom_field_name>
                    <custom_field_value>10</custom_field_value>
                    <custom_field_is_hidden>1</custom_field_is_hidden>
                </custom_field>
            </custom_fields>
            <transaction_details><% if $OrderDetails %><% loop $OrderDetails %>
                <transaction_detail>
                    <product_name>$Title</product_name>
                    <product_price>$Price</product_price>
                    <product_quantity>$Quantity</product_quantity>
                    <product_weight>$Weight</product_weight>
                    <product_code></product_code>
                    <parent_code></parent_code>
                    <image></image>
                    <url></url>
                    <length>$Length</length>
                    <width>$Width</width>
                    <height>$Height</height>
                    <expires></expires>
                    <sub_token_url></sub_token_url>
                    <subscription_nextdate>0000-00-00</subscription_nextdate>
                    <subscription_enddate>0000-00-00</subscription_enddate>
                    <is_future_line_item>0</is_future_line_item>
                    <subscription_frequency>1m</subscription_frequency>
                    <subscription_startdate>2007-07-07</subscription_startdate>
                    <shipto>John Doe</shipto>
                    <category_description>$CategoryDescription</category_description>
                    <category_code>$CategoryCode</category_code>
                    <product_delivery_type>$DeliveryType</product_delivery_type>
                    <transaction_detail_options><% if $Options %><% loop $Options %>
                        <transaction_detail_option>
                            <product_option_name>$Name</product_option_name>
                            <product_option_value>$OptionValue</product_option_value>
                            <price_mod>$PriceMod</price_mod>
                            <weight_mod>$WeightMod</weight_mod>
                        </transaction_detail_option><% end_loop %><% end_if %>
                    </transaction_detail_options>
                </transaction_detail><% end_loop %><% end_if %>
            </transaction_details>
            <shipto_addresses>
                <shipto_address>
                    <address_name>John Doe</address_name>
                    <shipto_first_name>John</shipto_first_name>
                    <shipto_last_name>Doe</shipto_last_name>
                    <shipto_address1>2345 Some Address</shipto_address1>
                    <shipto_address2></shipto_address2>
                    <shipto_city>Some City</shipto_city>
                    <shipto_state>TN</shipto_state>
                    <shipto_postal_code>37013</shipto_postal_code>
                    <shipto_country>US</shipto_country>
                    <shipto_shipping_service_description>DHL: Next Afternoon</shipto_shipping_service_description>
                    <shipto_subtotal>52.15</shipto_subtotal>
                    <shipto_tax_total>6.31</shipto_tax_total>
                    <shipto_shipping_total>15.76</shipto_shipping_total>
                    <shipto_total>74.22</shipto_total>
                    <shipto_custom_fields>
                        <shipto_custom_field>
                            <shipto_custom_field_name>My_Custom_Info</shipto_custom_field_name>
                            <shipto_custom_field_value>john's stuff</shipto_custom_field_value>
                        </shipto_custom_field>
                        <shipto_custom_field>
                            <shipto_custom_field_name>More_Custom_Info</shipto_custom_field_name>
                            <shipto_custom_field_value>more of john's stuff</shipto_custom_field_value>
                        </shipto_custom_field>
                    </shipto_custom_fields>
                </shipto_address>
            </shipto_addresses>
        </transaction>
    </transactions>
</foxydata>
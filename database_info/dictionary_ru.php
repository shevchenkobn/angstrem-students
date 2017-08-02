<?php
    return [
        "db_structure" =>[
            'contracts_info' => ["Контракты",
                "contract_number" => "Номер контракта",
                "balance" => "Остаток",
                "conclusion_date" => "Дата заключения",
                "activation_date" => "Активация аккаунта",
                "deactivation_date" => "Деактивация аккаунта"],
            'parents' => ["Родители",
                "contract_number" => "Номер контракта",
                "mother_fullname" => "ФИО матери",
                "mother_email" => "E-mail матери",
                "mother_phone" => "Телефон матери",
                "father_fullname" => "ФИО отца",
                "father_email" => "E-mail отца",
                "father_phone" => "Телефон отца",
                "postal_office" => "Отделение Новой почты"],
            'payments' => ["Оплаты",
                "payment_id" => "Номер оплаты",
                "contract_number" => "Номер контракта",
                "start_period" => "Начало периода оплаты",
                "end_period" => "Конец периода оплаты",
                "payment_timestamp" => "Дата и время оплаты"],
            'students' => ["Ученики",
                "contract_number" => "Номер контракта",
                "name" => "Имя",
                "second_name" => "Отчество",
                "surname" => "Фамилия",
                "form_number" => "Класс",
                "form_letter" => "Буква класса"],
            'students_info' => ["Медданные",
                "contract_number" => "Номер контракта",
                "medical_features" => "Медицинские особенности",
                "psychological_features" => "Психологические особенности"]
        ],
        "errors" => [
            "Произошла ошибка.",
            "no_columns" => "Не выбраны колонки для отображения.",
            "no_rows" => "Запрос не вернул результатов."
        ]
    ];
?>

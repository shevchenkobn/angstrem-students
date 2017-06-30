<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 30.06.17
 * Time: 8:33
 */
function translate($table, $column = null)
{
    $dictionary = [
        'conracts_info' => ["Контракты",
            "contract_number" => "Номер контракта",
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
            "contract_number" => "Номер контракта",
            "start_period" => "Начало периода оплаты",
            "end_period" => "Конец периода оплаты",
            "sum" => "Сумма"],
        'students' => ["Ученики",
            "contract_number" => "Номер контракта",
            "name" => "Имя",
            "surname" => "Фамилия",
            "form_number" => "Класс",
            "form_letter" => "Буква класса"],
        'students_info' => ["Медданные",
            "contract_number" => "Номер контракта"]
    ];
    $translation = $column === null ? $table : $column;
    if (key_exists($table, $dictionary))
        if ($column === null)
        {
            $translation = $dictionary[$table][0];
        }
        else
            if (key_exists($column, $dictionary[$table]))
                $translation = $dictionary[$table][$column];
    return $translation;
}
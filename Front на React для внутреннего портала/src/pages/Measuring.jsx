import React, {useContext, useEffect, useState} from 'react';
import {MDBContainer, MDBIcon, MDBBtn, MDBTooltip} from "mdb-react-ui-kit";
import {useIsToken} from "../hooks/useIsToken";
import MeasuringAPI from "../API/MeasuringAPI";
import 'animate.css';
import '../styles/Measuring.css'
import CustomerData from "../components/measuring/CustomerData";
import FloorsData from "../components/measuring/FloorsData";
import ObjectData from "../components/measuring/ObjectData";
import Submit from "../components/measuring/Submit";
import {MainContext, MeasuringContext} from "../context";
import Navbar from "../components/navbar";

const Measuring = () => {
    //Хуки
    useIsToken(); //Пользовательский хук, кторый проверяет есть ли в куки токен
    useEffect(() => {
        getMeasuringParams()
    }, []); //Хук, который срабатывает при перезагрузке страницы

    //функции
    function getMeasuringParams(){
        const measuring = JSON.parse(localStorage.getItem('measuring'));

        if (measuring){
            measuringContext.customerData = measuring.customerData;
            measuringContext.showedFloor = measuring.showedFloor;
            measuringContext.commentObject = measuring.commentObject;
            measuringContext.toWorkObject = measuring.toWorkObject;
            measuringContext.dataObject = measuring.dataObject;
            measuringContext.showObjectData = measuring.showObjectData;
            measuringContext.floors = measuring.floors;
        } else{
            measuringContext.floors = [
                {
                    "id": 1,
                    "name": "0 этаж",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 2,
                    "name": "1 этаж",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 3,
                    "name": "2 этаж",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 4,
                    "name": "3 этаж",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 5,
                    "name": "1 доп. этаж 1",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 6,
                    "name": "2 доп. этаж 1",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 7,
                    "name": "3 доп. этаж 1",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 8,
                    "name": "1 доп. этаж 2",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 9,
                    "name": "2 доп. этаж 2",
                    "created_at": null,
                    "updated_at": null
                },
                {
                    "id": 10,
                    "name": "3 доп. этаж 2",
                    "created_at": null,
                    "updated_at": null
                }
            ];
            // measuringContext.floors = response.data.floors; // Данные можно получать при авторизации. Загрузка долгая 1 раз
        }

        measuringContext.materials = [
            {
                "id": 1,
                "name": "Гипс",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": 2,
                "name": "Гипсо-цемент",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": 3,
                "name": "Цемент",
                "created_at": null,
                "updated_at": null
            }
        ];
        measuringContext.works = [
            {
                "id": "1",
                "name": "Оштукатуривание лестницы как элемент",
                "param_one": "Количество",
                "param_two": "Цена",
                "param_three": "Уголки",
                "param_four": null,
                "type": "ladderWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "2",
                "name": "Оштукатуривание лестницы как элемент (с закруглением)",
                "param_one": "Количество",
                "param_two": "Цена",
                "param_three": "Уголки",
                "param_four": "Арочные Уголки",
                "type": "ladderWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "3",
                "name": "Оштукатуривание стен лестничного марша",
                "param_one": "М.кв.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "ladderWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "4",
                "name": "Оштукатуривание потолков по маякам",
                "param_one": "М.кв.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "ceilingWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "5",
                "name": "Оштукатуривание потолков без маяков",
                "param_one": "М.кв.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "ceilingWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "6",
                "name": "Оштукатуривание эркера (без вычета проема)",
                "param_one": "М.кв.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "complexElements",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "7",
                "name": "Оштукатуривание полукруглых стен",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "complexElements",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "8",
                "name": "Оштукатуривание полукруглых откосов",
                "param_one": "М.п.",
                "param_two": "Арочные Уголки",
                "param_three": null,
                "param_four": null,
                "type": "complexElements",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "9",
                "name": "Оштукатуривание арок",
                "param_one": "М.п.",
                "param_two": "Арочные Уголки",
                "param_three": null,
                "param_four": null,
                "type": "complexElements",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "10",
                "name": "Формирование угла 90°",
                "param_one": "Шт.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "complexElements",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "11",
                "name": "Работа в стесненных условиях",
                "param_one": "Количество",
                "param_two": "Цена",
                "param_three": null,
                "param_four": null,
                "type": "complexElements",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "12",
                "name": "Покраска металла",
                "param_one": "Цена",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "13",
                "name": "Проклейка разнородных примыканий",
                "param_one": "М.п.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "14",
                "name": "Уменьшение толщины откоса (пеноплекс)",
                "param_one": "М.п.",
                "param_two": "Ширина откоса",
                "param_three": "Толщина",
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "15",
                "name": "Уменьшение толщины откоса (гипсокартон)",
                "param_one": "М.п.",
                "param_two": "Ширина откоса",
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "16",
                "name": "Монтаж гипсокартона",
                "param_one": "Длина",
                "param_two": "Ширина",
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "17",
                "name": "Упаковка защитной пленкой",
                "param_one": "Цена",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "18",
                "name": "Нанесение насечек по старому основанию",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "19",
                "name": "Нанесение клея под гребенку",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "20",
                "name": "Грунтовка",
                "param_one": "М.кв.",
                "param_two": "М.п.",
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "21",
                "name": "Нанесение бетоноконтакта",
                "param_one": "М.кв.",
                "param_two": "М.п.",
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "22",
                "name": "Дисковка стен (по старому основанию)",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "23",
                "name": "Зачистка стен алмазной чашкой",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "24",
                "name": "Сплошное проклеивание фасадной сетки",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "25",
                "name": "Набивка железной сетки",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "26",
                "name": "Монтаж пластикового уголка",
                "param_one": "М.п.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "27",
                "name": "Монтаж профиля примыкания",
                "param_one": "М.п.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "preparatoryWork",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "28",
                "name": "Демонтаж старого основания ",
                "param_one": "М.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "29",
                "name": "Демонтаж радиатора отопления",
                "param_one": "Шт.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "30",
                "name": "Установка подрозетников",
                "param_one": "Шт.",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "31",
                "name": "Доплата за высоту стен выше 3,2 метр",
                "param_one": null,
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "32",
                "name": "Доплата за высоту стен выше 3,8 метра",
                "param_one": null,
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "33",
                "name": "Доплата за высоту стен выше 4,5 метра",
                "param_one": null,
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "34",
                "name": "Доплата за слой более 40 мм",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            },
            {
                "id": "35",
                "name": "Доплата за слой более 60 мм",
                "param_one": "М",
                "param_two": null,
                "param_three": null,
                "param_four": null,
                "type": "additionalWorks",
                "created_at": null,
                "updated_at": null
            }
        ];

        // measuringContext.materials = response.data.material; // Данные можно получать при авторизации. Загрузка долгая 1 раз
        // measuringContext.works = response.data.works; // Данные можно получать при авторизации. Загрузка долгая 1 раз

        setMeasuringContext({...measuringContext});
    } //Проверяет доступно ли пользователю и получет исходные данные
    async function calculateEstimate(e){
        mainContext.showLoad = true;
        setMainContext({...mainContext});

        e.preventDefault();

        const response = await MeasuringAPI.calculateEstimate(measuringContext);

        if(response.status == "error") {
            mainContext.info.info(response, "danger");
            return false;
        }

        console.log(response);

        mainContext.showLoad = false;
        setMainContext({...mainContext});
    } // Расчёт сметы
    const isNullObject = (object) => {
        let count = 0;
        let notNull = 0;
        for (let key in object) {
            count++;

            if(typeof object[key] == "object"){
                if(!isNullObject(object[key])) notNull++;
            } else{
                if(object[key] != '') notNull++;
            }
        }

        if(count != notNull || (count == 0 && notNull == 0) ){
            return false;
        } else{
            return true;
        }
    } //Проверяет пустой ли объект
    const clear = (e) => {
        e.preventDefault();

        if (!confirm("Замер будет сброшен.\r\nВы уверены?")) return false;
        localStorage.setItem('measuring', null);
        location.reload();
    }

    //константы
    const {mainContext, setMainContext} = useContext(MainContext); //глобальные данные
    const [measuringContext, setMeasuringContext] = useState({
        "customerData": {
            "name": "",
            "post": "",
            "measurer": "",
            "address": "",
            "phone": "",
            "typeSpace": ""
        },
        "showedFloor" : [],
        "materials": [],
        "works": [],
        "floors": [],
        "selectFloor": "",
        "isNullObject": {isNullObject},
        "commentObject": "",
        "toWorkObject": [],
        "dataObject": {
            "threeHundredEighty": "",
            "layer": "",
            "media": "",
            "water": "",
            "washing": "",
            "skidding": "",
            "carryingMore": false,
            "segmentation": "",
            "segmentationMultiChoice": {
                "fullGeometry": false,
                "subcontracting": false,
                "oldFoundation": false,
            },
            "electrics": "",
            "km": "",
            "cable": "",
            "tube": "",
            "concreteContact": {
                "km": "",
                "pm": "",
                "percent": "",
            },
            "gasBlock": {
                "km": "",
                "pm": "",
                "percent": "",
            }
        },
        "showObjectData": false,
    });
    const [localStorageState, setLocalStorageState] = useState(false);

    if (localStorageState){
        localStorage.setItem('measuring', JSON.stringify(measuringContext));
    }

    if (isNullObject(measuringContext.customerData)){
        if (!localStorageState) setLocalStorageState(true);
    }

    return (
        <MeasuringContext.Provider value={{measuringContext, setMeasuringContext}}>
            <Navbar active="measuring" />
            <MDBContainer fluid>
                <MDBContainer id="measuring" className="rounded shadow-4 bg-dark mt-5 pt-2 pb-4">
                    <form onSubmit={e => calculateEstimate(e)}>
                        {isNullObject(measuringContext.customerData) &&
                            <div className="text-end">
                                <MDBBtn
                                    onClick={e => clear(e)}
                                    className="clear-measuring"
                                    color='danger'
                                    floating
                                >
                                    <MDBTooltip tag='a' title="Сбросить замер">
                                        <MDBIcon fas icon="times"/>
                                    </MDBTooltip>
                                </MDBBtn>
                            </div>
                        }

                        <CustomerData/>

                        {measuringContext.showedFloor.map((sf, index) =>
                            <FloorsData sf={sf} index={index} key={index} />
                        )}

                        {isNullObject(measuringContext.customerData) &&
                            <ObjectData />
                        }

                        {isNullObject(measuringContext.customerData) &&
                            <Submit />
                        }
                    </form>
                </MDBContainer>
            </MDBContainer>
        </MeasuringContext.Provider>
    );
};

export default Measuring;
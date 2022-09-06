import React, {useContext, useRef} from 'react';
import {MeasuringContext} from "../../context";
import SelectSearch from 'react-select-search';
import '../../styles/SelectSearch.css'

const AddToWork = (props) => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные
    const addToWork = (params) => {
        console.log(params);

        let newAddWork = {
            "name": "",
            "paramOne": "",
            "paramTwo": "",
            "paramThree": "",
            "paramFour": "",
            "param_one": "",
            "param_two": "",
            "param_three": "",
            "param_four": "",
        };

        measuringContext.works.map(work => {
            if(work.id == params.id){
                newAddWork.name = work.name;
                newAddWork.param_one = work.param_one;
                newAddWork.param_two = work.param_two;
                newAddWork.param_three = work.param_three;
                newAddWork.param_four = work.param_four;

            }
        });

        if(params.roomIndex === false || params.index === false){
            if(params.roomIndex === false && params.index !== false){
                measuringContext.showedFloor[params.index].addWorks.push(newAddWork);
            } else {
                measuringContext.toWorkObject.push(newAddWork);
            }
        } else {
            measuringContext.showedFloor[params.index].rooms[params.roomIndex].addWorks.push(newAddWork);
        }

        setMeasuringContext({...measuringContext});
    }

    const searchInput = useRef();

    const options = [
        {
            type: "group",
            name: "Работа по лестнице",
            items: []
        },
        {
            type: "group",
            name: "Работа по потолку",
            items: []
        },
        {
            type: "group",
            name: "Сложные элементы",
            items: []
        },
        {
            type: "group",
            name: "Подготовительные работы",
            items: []
        },
        {
            type: "group",
            name: "Дополнительные работы",
            items: []
        },
    ];

    measuringContext.works.map((work, key) => {
        if (work.type == "ladderWork") options[0].items.push({"value": work.id, "name": work.name})
        if (work.type == "ceilingWork") options[1].items.push({"value": work.id, "name": work.name})
        if (work.type == "complexElements") options[2].items.push({"value": work.id, "name": work.name})
        if (work.type == "preparatoryWork") options[3].items.push({"value": work.id, "name": work.name})
        if (work.type == "additionalWorks") options[4].items.push({"value": work.id, "name": work.name})
    })

    const handleChange = (...args) => {
        const params = {
            "id": args[0],
            "index": props.index,
            "roomIndex": props.roomIndex
        };
        addToWork(params);
    };
    const handleFilter = (items) => {
        return (searchValue) => {
            if (searchValue.length === 0) {
                return options;
            }
            const updatedItems = items.map((list) => {
                const newItems = list.items.filter((item) => {
                    return item.name.toLowerCase().includes(searchValue.toLowerCase());
                });
                return { ...list, items: newItems };
            });
            return updatedItems;
        };
    };

    return (

        <SelectSearch
            ref={searchInput}
            options={options}
            filterOptions={handleFilter}
            name="Workshop"
            placeholder={props.label}
            search
            onChange={handleChange}
        />
        // <select defaultValue="" onChange={e => addToWork({"e": e, "index": props.index, "roomIndex": props.roomIndex})} className="select-css h-100">
        //     <option value="" disabled>{props.label}</option>
        //     {measuringContext.works.map(work =>
        //         <option value={work.id} key={work.id}>{work.name}</option>
        //     )}
        // </select>
    );
};

export default AddToWork;
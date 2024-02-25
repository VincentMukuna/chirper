import {usePage} from "@inertiajs/react";

export default function useSearchParams(){
    const url = usePage().url
    const searchStr = url.split('?').at(1)
    const params = searchStr?searchStr.split('&').map(str=>{
        return str.split('=');
    }):[];

    return new Map(params);
}

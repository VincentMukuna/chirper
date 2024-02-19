import React, {useState} from "react";
import dayjs from "dayjs";
import relativeTime from 'dayjs/plugin/relativeTime';
import {Link, useForm, usePage,router} from "@inertiajs/react";
import Dropdown from "@/Components/Dropdown.jsx";
import InputError from "@/Components/InputError.jsx";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import {IconChirp, IconRechirp, IconReply} from "@/Components/Icons.jsx";

dayjs.extend(relativeTime);

export default function Chirp({chirp}) {
    const {auth} = usePage().props;

    const [isLike, setIsLike] = useState(chirp.isLike);
    const [isRechirped, setIsRechirped] = useState(chirp.isRechirp);
    const [likes, setLikes] = useState(chirp.likes_count);
    const [rechirps, setRechirps] = useState(chirp.rechirps_count);
    const  [editing, setEditing] = useState(false);

    const {data, setData, patch, clearErrors, reset, errors} = useForm({
        message: chirp.message,
    });

    if (chirp.rechirping){
        console.log('Chirp', chirp)
    }
    const onToggleLike=()=>{router.patch(route(isLike?'chirps.unlike':'chirps.like', {chirp:chirp.id}),
            {},
            {
                preserveScroll:true,
                preserveState:true,
            })
        setLikes(prev=>isLike?prev-1:prev+1)
        setIsLike(prev=>!prev)
    }

    function getRechirpPrams(){
        if(chirp.rechirping){
            return {chirp:chirp.rechirping}
        }return {chirp: chirp.id}
    }


    const onToggleRechirp=()=>{router.post(route(isRechirped?'chirps.undo_rechirp':'chirps.rechirp',getRechirpPrams() ),
                {},
                {
                    preserveScroll:true,
                    preserveState:true,
                })
            setRechirps(prev=>isRechirped?prev-1:prev+1)
            setIsRechirped(prev=>!prev)
    }



    const submit = (e) => {
        e.preventDefault();
        patch(route('chirps.update', chirp), {
            onSuccess: () => {
                setEditing(false);
            }
        });
    }
    return (
        <div className="p-6 flex space-x-4 hover:bg-gray-50 cursor-pointer transition-all">
            {chirp.replying_to!==null
                ? <Link href={route('chirps.show', {chirp: chirp.replying_to})}>
                    <IconReply />
                </Link>
                :
                chirp.rechirping!==null?<IconRechirp />
                : <IconChirp />}

            <div className="flex-1">
                <div className="flex justify-between items-center">
                    <div className='flex items-center gap-2'>
                        {chirp.rechirping
                            ?
                            <>
                                <Link
                                    href={route('user.profile', {user: chirp.original_chirp.user.id})}
                                    className="text-gray-800 hover:underline"
                                >
                                    {chirp.original_chirp.user.name}
                                </Link>
                                <span className={'text-gray-500'}>via</span>

                            </>
                            :null}
                        <Link
                            href={route('user.profile', {user:chirp.user.id})}
                            className="text-gray-800 hover:underline"
                        >
                            {chirp.user.name}
                        </Link>



                        <small className="ml-2 text-xs text-gray-500">{dayjs(chirp.created_at).fromNow()}</small>
                        {chirp.rechirping?<Link href={route('chirps.show', {chirp:chirp.rechirping})} className="ml-2 text-xs text-blue-800 hover:underline">see rechirped chirp</Link>:null}
                        {chirp.created_at !== chirp.updated_at &&
                            <small className="text-xs text-gray-700">&middot; edited</small>}
                    </div>

                    {chirp.user.id === auth.user.id &&
                        <Dropdown >
                            <Dropdown.Trigger >
                                <button>
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 text-gray-400"
                                         viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                </button>
                            </Dropdown.Trigger>
                            <Dropdown.Content>
                                <button
                                    className="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:bg:gray-100 transition duration-150 ease-in-out"
                                    onClick={() => setEditing(true)}>
                                    Edit
                                </button>
                                <Dropdown.Link as="button" href = {route('chirps.destroy', chirp.id)} method = "delete">
                                    Delete
                                </Dropdown.Link>
                            </Dropdown.Content>
                        </Dropdown>
                    }
                </div>
                {editing
                    ? <form onSubmit={submit}>
                    <textarea value={data.message} onChange={e => setData('message', e.target.value)}
                              className="mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                        <InputError message={errors.message} className="mt-2"/>
                        <div className="space-x-2">
                            <PrimaryButton className="mt-4">Save</PrimaryButton>
                            <button className="mt-4" onClick={() => {
                                setEditing(false);
                                reset();
                                clearErrors();
                            }}>Cancel
                            </button>
                        </div>
                    </form>
                    : <Link  href={route('chirps.show', [chirp.id])} className="mt-3 text-lg test-gray-900 flex flex-col gap-1">
                        {chirp.message}

                        {chirp.rechirping?null:<div className={'flex gap-8 items-center'}>
                            {(chirp.isLike === null || chirp.isLike === undefined)
                                ? null
                                : <button onClick={(e) => {
                                    e.preventDefault();
                                    onToggleLike()
                                }} className="tex-xs text-gray-400 hover:scale-105 transition">
                                    {isLike ? "❤️" : "🤍"} <span className='text-sm'>{likes}</span>
                                </button>}
                            {(chirp.isRechirp === null || chirp.isRechirp === undefined)
                                ? null
                                : <button onClick={(e) => {
                                    e.preventDefault();
                                    onToggleRechirp()
                                }} className="tex-xs text-gray-400  hover:scale-105 transition flex gap-2">
                                    <IconRechirp className={`${isRechirped ? 'text-indigo-800' : ''} `}/> <span
                                    className='text-sm'>{rechirps}</span>
                                </button>}
                        </div>}

                    </Link>
                }
            </div>



        </div>
    )
}

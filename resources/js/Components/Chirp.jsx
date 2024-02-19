import React, {useState} from "react";
import dayjs from "dayjs";
import relativeTime from 'dayjs/plugin/relativeTime';
import {Link, useForm, usePage,router} from "@inertiajs/react";
import Dropdown from "@/Components/Dropdown.jsx";
import InputError from "@/Components/InputError.jsx";
import PrimaryButton from "@/Components/PrimaryButton.jsx";

dayjs.extend(relativeTime);

export default function Chirp({chirp}) {
    const {auth} = usePage().props;

    const [isLike, setIsLike] = useState(chirp.isLike);
    const [likes, setLikes] = useState(chirp.likes_count);
    const  [editing, setEditing] = useState(false);

    const {data, setData, patch, clearErrors, reset, errors} = useForm({
        message: chirp.message,
    })
    const onToggleLike=()=>{
        setIsLike(prev=>!prev);
        if(chirp.isLike){

            setLikes(prev=>prev-1)
            router.patch(
                route('chirps.dislike', {chirp:chirp.id}),
                {}
                ,
                {
                    onError:(e)=>{
                        chirp.isLike = false;
                        console.log('Error: ', e)
                        setLikes(prev=>prev+1)
                        setIsLike(prev=>!prev);
                    },
                    preserveScroll:true,
                    preserveState:true,
                })
        }else{
            chirp.isLike = true;
            setLikes(prev=>prev+1)
            router.patch(
                route('chirps.like', {chirp:chirp.id}),
                {},
                {
                    onError:(e)=>{
                        chirp.isLike = false;
                        console.log('Error: ', e)
                        setLikes(prev=>prev-1)
                        setIsLike(prev=>!prev);
                    },
                    preserveScroll:true,
                    preserveState:true,
                })
        }

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
        <div className="p-6 flex space-x-2 hover:bg-gray-50 cursor-pointer transition-all">
            {chirp.replying_to!==null
                ? <Link href={route('chirps.show', {chirp: chirp.replying_to})}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                        <path fill="#888888"
                              d="M10 9V7.41c0-.89-1.08-1.34-1.71-.71L3.7 11.29a.996.996 0 0 0 0 1.41l4.59 4.59c.63.63 1.71.19 1.71-.7V14.9c5 0 8.5 1.6 11 5.1c-1-5-4-10-11-11"/>
                    </svg>
                </Link>

                : <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-gray-600 -scale-x-100" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                    <path strokeLinecap="round" strokeLinejoin="round"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>}

            <div className="flex-1">
                <div className="flex justify-between items-center">
                    <div className='flex items-center gap-2'>
                        <Link href={route('user.profile', {user:chirp.user.id})} className="text-gray-800 hover:underline">{chirp.user.name}</Link>

                        <small className="ml-2 text-xs text-gray-500">{dayjs(chirp.created_at).fromNow()}</small>
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
                        {(chirp.isLike===null||chirp.isLike===undefined)
                            ?null
                            :<button onClick={(e) => {
                                e.preventDefault();
                                onToggleLike()
                            }} className="tex-xs text-gray-400 self-start hover:scale-105 transition">
                                {isLike ? "❤️" : "🤍"} <span className='text-sm'>{likes}</span>
                            </button>}
                    </Link>
                }
            </div>



        </div>
    )
}

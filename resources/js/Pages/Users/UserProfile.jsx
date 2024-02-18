import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {Head, router} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import {Tab} from "@headlessui/react";
import Chirp from "@/Components/Chirp.jsx";
import {useState} from "react";
import {cn} from "@/lib/utils.js";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";

dayjs.extend(relativeTime);

export default function UserProfile({auth, user, userFollows}){
    const [selectedTab, setSelectedTab] = useState(0);
    const onToggleFollow=()=>{
        router.post(
            route('user.toggle-follow', {user:user.id}),
            {},
            {
                preserveScroll:true,
                preserveState:true,
            })
    }

    return(
        <AuthenticatedLayout user={auth.user}>
            <Head title={user.name}/>

            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">

                <div className="bg-white p-4 sm:p-6 lg:p-8 rounded-lg shdaow-lg  gap-2 flex flex-col">
                    <div className='w-32 h-32 bg-gray-300 rounded-full'>

                    </div>
                    <div className='grid '>
                        <span className='font-semibold text-lg'>{user.name}</span>
                        <span className='italic '>{user.bio || "A bio"}</span>

                    </div>

                    <div className='flex gap-1 items-center text-sm'>
                        <span className={'text-gray-500 '}>Joined</span>
                        <span>{dayjs(user.created_at).fromNow()}</span>

                    </div>

                    <div className='flex justify-between items-center'>
                        <div className='flex gap-4'>
                            <span className='font-semibold text-lg'>{`${user.following_count} following`}</span>
                            <span className='font-semibold text-lg'>{`${user.followers_count} followers`}</span>

                        </div>

                        <div className=''>
                            {user.id === auth.user.id
                                ? null :
                                <PrimaryButton
                                    onClick={() => onToggleFollow()}>{userFollows ? "Unfollow" : "Follow"}</PrimaryButton>
                            }
                        </div>
                    </div>


                    <Tab.Group selectedIndex={selectedTab} onChange={(val)=>setSelectedTab(val)}  >
                        <Tab.List className='flex gap-4 justify-around mt-6 '>
                            <Tab  className={cn('p-1 border-b-2 border-transparent focus-visible:outline-none', {'border-indigo-800':selectedTab===0})}>Chirps</Tab>
                            <Tab className={cn('p-1 border-b-2 border-transparent focus-visible:outline-none', {'border-indigo-800':selectedTab===1})}>Replies</Tab>
                            <Tab className={cn('p-1 border-b-2 border-transparent focus-visible:outline-none', {'border-indigo-800':selectedTab===2})}>Likes</Tab>
                        </Tab.List>
                        <Tab.Panels>
                            <Tab.Panel tabIndex={1}>
                                <div className='divide-y'>
                                    {user.chirps.map(chirp => (
                                        <Chirp chirp={chirp} key={chirp.id} />
                                    ))}
                                </div>
                            </Tab.Panel>
                            <Tab.Panel tabIndex={2}>
                                <div className='divide-y'>
                                    {user.replies.map(chirp => (
                                        <Chirp chirp={chirp} key={chirp.id}/>
                                    ))}
                                </div>
                            </Tab.Panel>
                            <Tab.Panel tabIndex={3}>
                                <div className='divide-y'>
                                    {user.liked_chirps.map(liked_chirp=>(
                                        <Chirp chirp={liked_chirp.chirp} key={liked_chirp.id} />
                                    ))}
                                </div>
                            </Tab.Panel>
                        </Tab.Panels>
                    </Tab.Group>
                </div>
                {/*<div>*/}
                {/*    <pre>*/}
                {/*        <code>*/}
                {/*            {JSON.stringify(user, null, 2)}*/}
                {/*        </code>*/}
                {/*    </pre>*/}
                {/*</div>*/}
            </div>
        </AuthenticatedLayout>
    )
}

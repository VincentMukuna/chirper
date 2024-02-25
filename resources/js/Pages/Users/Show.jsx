import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {Head, router} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import {Tab} from "@headlessui/react";
import {Chirp, ChirpActions, ChirpAvatar, ChirpBody, ChirpContent, ChirpHeader} from "@/Components/Chirp.jsx";
import {useState} from "react";
import {cn} from "@/lib/utils.js";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import SecondaryButton from "@/Components/SecondaryButton.jsx";
import Modal from "@/Components/Modal.jsx";
import UserFollowRshipList from "@/Pages/Users/Partials/UserFollowRshipList.jsx";
import useSearchParams from "@/hooks/useSearchParams.jsx";

dayjs.extend(relativeTime);

export default function Show({auth, user}){
    const tabs = [
        {id :'chirps',name: 'Chirps', chirps: user.posts},
        {id :'replies',name: 'Replies', chirps: user.replies},
        {id :'likes',name: 'Likes', chirps: user.liked_chirps.map(likedChirp=>likedChirp.chirp)}
    ]
    const params = useSearchParams();

    const [selectedTab, setSelectedTab] = useState(tabs.findIndex(tab=>tab.id===params.get('tab'))||'0');
    const [showingFollowRships, setShowingFollowRships]=useState(undefined);

    function onCloseModal(){
        setShowingFollowRships(undefined);
    }





    const onToggleFollow=()=>{
        router.post(
            route('users.toggle-follow', {user:user.id}),
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
                        <span className=' '>{user.bio || "A bio"}</span>

                    </div>

                    <div className='flex gap-1 items-center text-sm'>
                        <span className={'text-gray-500 '}>Joined</span>
                        <span>{dayjs(user.created_at).fromNow()}</span>

                    </div>

                    <div className='flex justify-between items-center'>
                        <div className='flex gap-4'>
                            <button
                                onClick={()=>{user.following_count>0&&setShowingFollowRships('following')}}
                                aria-label={'show following'}
                                className='font-semibold text-lg hover:underline'
                            >
                                {`${user.following_count} following`}
                            </button>
                            <button
                                onClick={()=>{user.followers_count>0&&setShowingFollowRships('followers')}}
                                aria-label={'show followers'}
                                className='font-semibold text-lg hover:underline'
                            >
                                {`${user.followers_count} followers`}
                            </button>
                            <Modal maxWidth={'lg'} show={showingFollowRships!==undefined} onClose={onCloseModal}>
                                <div className={'p-6'}>
                                    {
                                        <UserFollowRshipList user={user} list={showingFollowRships} />
                                    }
                                </div>
                            </Modal>
                        </div>

                        <div className=''>
                            {user.id === auth.user.id
                                ? <SecondaryButton onClick={()=>router.get(route('profile.edit'))}>
                                    Edit Profile
                                </SecondaryButton>
                                :
                                user.is_following
                                    ?
                                    <SecondaryButton
                                        onClick={() => onToggleFollow()}
                                    >
                                        Unfollow
                                    </SecondaryButton>
                                    :
                                    <PrimaryButton
                                        onClick={() => onToggleFollow()}

                                    >
                                        Follow
                                    </PrimaryButton>
                            }
                        </div>
                    </div>


                    <Tab.Group selectedIndex={selectedTab} onChange={(val)=> {
                        history.pushState(null, '', `?tab=${tabs[val].id}`)
                        setSelectedTab(val)
                    }}  >
                        <Tab.List className='flex gap-4 justify-around mt-6 border-y py-2 '>
                            {tabs.map((tab, index)=>(
                                <Tab
                                    value={tab.id}
                                    key={tab.id}
                                    className={({selected})=>cn('py-2 px-4 rounded-md', selected?'bg-gray-100':'')}
                                >
                                    {tab.name}
                                </Tab>
                            ))}
                        </Tab.List>
                        <Tab.Panels>
                            {tabs.map((tab, index)=>(
                                <Tab.Panel id={tab.id} key={tab.id} className='mt-4'>
                                    <div className={'flex flex-col divide-y'}>
                                        {tab.chirps.map(chirp=>(
                                            <Chirp key={chirp.id} chirp={chirp}>
                                                <ChirpAvatar/>
                                                <ChirpContent>
                                                    <ChirpHeader/>
                                                    <ChirpBody/>
                                                    <ChirpActions/>
                                                </ChirpContent>
                                            </Chirp>
                                        ))}
                                    </div>
                                </Tab.Panel>
                            ))}
                        </Tab.Panels>
                    </Tab.Group>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {Head, useForm, usePage} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton.jsx";

export default function UserProfile({auth, user, userFollows, warning}){
    const {data, setData, post, processing, reset, errors, } = useForm({

    })
    const onToggleFollow=()=>{
        post(route('user.toggle-follow', {user:user.id}))
    }
    return(
        <AuthenticatedLayout user={auth.user}>
            <Head title={user.name}/>

            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">

                <div className="bg-white rounded-lg shdaow-lg p-3 gap-2 flex flex-col items-center ">
                    <div className='grid place-items-center'>
                        <span className='font-semibold text-lg'>{user.name}</span>
                        <span className='italic '>{user.bio || "A bio"}</span>

                    </div>
                    <div className='flex gap-4'>
                        <span className='font-semibold text-lg'>{`${user.following_count} following`}</span>
                        <span className='font-semibold text-lg'>{`${user.followers_count} followers`}</span>

                    </div>

                    <div>
                        {user.id!==auth.id
                            ? null :
                            <PrimaryButton
                                onClick={() => onToggleFollow()}>{userFollows ? "Unfollow" : "Follow"}</PrimaryButton>
                        }</div>
                </div>

            </div>
        </AuthenticatedLayout>
)
}

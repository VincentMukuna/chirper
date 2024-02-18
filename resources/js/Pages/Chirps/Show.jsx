import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import {Head, Link, usePage} from "@inertiajs/react";
import dayjs from "dayjs";
import Dropdown from "@/Components/Dropdown.jsx";
import Chirp from "@/Components/Chirp.jsx";
import ReplyForm from "@/Pages/Chirps/Partials/ReplyForm.jsx";

export default function Show({chirp, auth}){
    const props = usePage()
    console.log("PROPS: ", props);
    return(
        <AuthenticatedLayout user={auth.user}>
            <Head title={`Chirp by ${chirp.user.name}: ${chirp.message}`}/>
            <main className='max-w-2xl mx-auto p-4 sm:p-6 lg:p-8'>
                <div className='p-4 bg-white shadow-sm rounded-lg '>
                    <Chirp chirp={chirp}/>
                    <div className='ms-12 my-4'>
                        <ReplyForm chirp={chirp}/>
                    </div>

                    <div className='flex flex-col border-l ms-12'>
                        {chirp.replies.map(reply=>(
                            <Chirp key={reply.id} chirp={reply} />
                        ))}
                    </div>

                </div>
            </main>
        </AuthenticatedLayout>

    )
}

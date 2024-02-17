import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import LikeChirpNotification from "@/Components/Notifications/LikeChirpNotifiaction.jsx";
import {Head} from "@inertiajs/react";
import NewChirpNotification from "@/Components/Notifications/NewChirpNotification.jsx";

export default function Index({auth, notifications}){
    function renderNotification(notification){
        switch(notification.type){
            case 'App\\Notifications\\LikeChirp':
                return <LikeChirpNotification key={notification.id} notification={notification} />
            case 'App\\Notifications\\NewChirp':
                return <NewChirpNotification key={notification.id} notification={notification} />
            default:
                return null;
        }
    }
    return(
        <AuthenticatedLayout user={auth.user}>
            <Head title="Notifications" />
            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
                <div className="mt-6 bg-white shadow-sm rounded-lg divide-y">
                    {notifications.map((notification) => (
                        renderNotification(notification)
                    ))}
                </div>
            </div>

        </AuthenticatedLayout>

    )
}

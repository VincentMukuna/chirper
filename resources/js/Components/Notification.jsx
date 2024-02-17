import React from "react";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import {cn} from "@/lib/utils.js";
import Dropdown from "@/Components/Dropdown.jsx";
dayjs.extend(relativeTime);

function Notification({notification, icon='🔔', title='New notification', body = 'Hey, there sth big happened'}){
    return(
        <div className={cn("flex  p-6 gap-3 ",
            {
                "bg-gray-50": notification.read_at === null
            }
        )}>
            <div className="flex-shrink-0 text-lg">
                {icon}
            </div>
            <div className='ms-4 flex flex-col gap-2 w-full'>
                <div className="flex justify-between ">
                    <div className="flex  gap-2 ">
                        <div className="text-sm font-medium text-gray-900">
                            {title}
                        </div>
                        <div className="text-sm text-gray-400">
                            {dayjs(notification.created_at).fromNow()}
                        </div>
                    </div>


                    <Dropdown>
                        <Dropdown.Trigger className='hidden'>
                            <button>
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 text-gray-400"
                                     viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                            </button>
                        </Dropdown.Trigger>
                        <Dropdown.Content>
                            {notification.read_at?null:<Dropdown.Link as="button" href={route('notifications.mark-as-read', notification.id)}
                                            method="patch">
                                Mark as Read
                            </Dropdown.Link>}
                            <Dropdown.Link as="button" href = {route('notifications.destroy', notification.id)} method = "delete">
                                Delete
                            </Dropdown.Link>
                        </Dropdown.Content>
                    </Dropdown>
                </div>
                <div className={cn("line-clamp-1", {
                    "font-bold": notification.read_at === null

                })}>
                    {body}
                </div>
            </div>

        </div>

    )
}

export default Notification;

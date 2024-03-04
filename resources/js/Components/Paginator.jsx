export default function Paginator({links}){

    return (
        <pre>
            <code>
                {JSON.stringify(links,null, 2)}
            </code>
        </pre>
    )
}

import Layout from "./Components/Layout"

export default function HomePage() {
    return(
        <div>This is a test</div>
    )
}

HomePage.layout = page => <Layout children={page} slot="Home Page" />
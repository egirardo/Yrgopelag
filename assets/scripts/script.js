const url = 'https://www.yrgopelag.se/centralbank/withdraw' 

        async function getData() {
            try {
                const response = await fetch(url)
                const result = await response.json()
                console.log(result)
            } catch (error) {
                console.error(error)
            }

        }

        getData();